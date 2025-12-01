const express = require('express');
const cors = require('cors');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const db = require('./config/database');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3002;
const JWT_SECRET = process.env.JWT_SECRET || 'your-secret-key';

console.log('DB Config:', {
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD ? '***' : 'EMPTY',
  database: process.env.DB_NAME
});

app.use(cors({
  origin: ['http://localhost:8080', 'http://127.0.0.1:8080', 'http://127.0.0.1:5500', 'http://localhost:5500', 'http://localhost:4200'],
  credentials: true
}));
app.use(express.json());

function getNextDrawDate(dayName) {
  const days = { monday: 1, wednesday: 3, friday: 5 };
  const now = new Date();
  const targetDay = days[dayName];
  const currentDay = now.getDay();
  const currentHour = now.getHours();
  
  let daysUntilNext = targetDay - currentDay;
  
  if (daysUntilNext === 0 && currentHour >= 20) {
    daysUntilNext = 7;
  }

  else if (daysUntilNext < 0) {
    daysUntilNext += 7;
  }
  
  const nextDate = new Date(now);
  nextDate.setDate(now.getDate() + daysUntilNext);
  nextDate.setHours(20, 0, 0, 0);
  
  return nextDate.toISOString();
}

function generateRandomNumbers(count, min, max) {
  const numbers = [];
  while (numbers.length < count) {
    const num = Math.floor(Math.random() * (max - min + 1)) + min;
    if (!numbers.includes(num)) {
      numbers.push(num);
    }
  }
  return numbers.sort((a, b) => a - b);
}

function authenticateToken(req, res, next) {
  const authHeader = req.headers['authorization'];
  const token = authHeader && authHeader.split(' ')[1];

  if (!token) {
    return res.status(401).json({ error: 'Access token required' });
  }

  jwt.verify(token, JWT_SECRET, (err, user) => {
    if (err) {
      return res.status(403).json({ error: 'Invalid token' });
    }
    req.user = user;
    next();
  });
}

app.get('/api/health', (req, res) => {
  res.json({ status: 'API is running' });
});


app.post('/api/register', async (req, res) => {
  const { email, password, confirmPassword } = req.body;
  
  try {
    if (!email || !password || !confirmPassword) {
      return res.status(400).json({ error: 'All fields are required' });
    }
    
    if (password !== confirmPassword) {
      return res.status(400).json({ error: 'Passwords do not match' });
    }
    
    if (password.length < 6) {
      return res.status(400).json({ error: 'Password must be at least 6 characters' });
    }
    
    const [existingUsers] = await db.execute('SELECT id FROM users WHERE email = ?', [email]);
    if (existingUsers.length > 0) {
      return res.status(400).json({ error: 'User already exists' });
    }
    
    const hashedPassword = await bcrypt.hash(password, 10);

    const [result] = await db.execute(
      'INSERT INTO users (email, password) VALUES (?, ?)',
      [email, hashedPassword]
    );
    
    const userId = result.insertId;
    
    const token = jwt.sign({ id: userId, email }, JWT_SECRET, { expiresIn: '24h' });
    
    res.json({ 
      success: true, 
      message: 'Registration successful',
      token,
      user: { id: userId, email }
    });
  } catch (error) {
    console.error('Registration error:', error);
    res.status(500).json({ error: 'Registration failed' });
  }
});

app.post('/api/login', async (req, res) => {
  const { email, password } = req.body;
  
  try {
    if (!email || !password) {
      return res.status(400).json({ error: 'Email and password are required' });
    }
    
    const [users] = await db.execute('SELECT * FROM users WHERE email = ?', [email]);
    if (users.length === 0) {
      return res.status(400).json({ error: 'Invalid credentials' });
    }
    
    const user = users[0];
    
    const validPassword = await bcrypt.compare(password, user.password);
    if (!validPassword) {
      return res.status(400).json({ error: 'Invalid credentials' });
    }
    
    const token = jwt.sign({ id: user.id, email: user.email }, JWT_SECRET, { expiresIn: '24h' });
    
    res.json({ 
      success: true, 
      message: 'Login successful',
      token,
      user: { id: user.id, email: user.email }
    });
  } catch (error) {
    console.error('Login error:', error);
    res.status(500).json({ error: 'Login failed' });
  }
});

app.get('/api/draws', async (req, res) => {
  try {
    const [poolData] = await db.execute(`
      SELECT lottery, draw_date, COUNT(*) * 0.001 as pool_amount 
      FROM entries 
      GROUP BY lottery, draw_date
      ORDER BY draw_date
    `);
    
    const draws = [];
    let id = 1;
    
    poolData.forEach(row => {
      const drawDate = new Date(row.draw_date);
      drawDate.setHours(20, 0, 0, 0);
      
      draws.push({
        id: id++,
        name: row.lottery,
        jackpot: `$${parseFloat(row.pool_amount).toFixed(3)}`,
        nextDraw: drawDate.toISOString()
      });
    });
    
    // Ensure we have exactly 6 draws by adding future draws if needed
    const lotteries = ['Mon Lotto', 'Wed Lotto', 'Fri Lotto'];
    const days = ['monday', 'wednesday', 'friday'];
    
    while (draws.length < 6) {
      const lotteryIndex = (draws.length) % 3;
      const lottery = lotteries[lotteryIndex];
      const nextDraw = getNextDrawDate(days[lotteryIndex]);
      const futureDate = new Date(nextDraw);
      futureDate.setDate(futureDate.getDate() + Math.floor(draws.length / 3) * 7);
      
      draws.push({
        id: draws.length + 1,
        name: lottery,
        jackpot: '$0.000',
        nextDraw: futureDate.toISOString()
      });
    }
    
    res.json(draws.slice(0, 6));
  } catch (error) {
    console.error('Error fetching draws:', error);
    res.status(500).json({ error: 'Failed to fetch draws' });
  }
});

app.get('/api/results', async (req, res) => {
  try {
    const [results] = await db.execute(`
      SELECT lottery, winning_numbers, bonus_numbers, draw_date, total_pool_money
      FROM results 
      ORDER BY draw_date DESC
      LIMIT 10
    `);
    
    const formattedResults = results.map(result => ({
      game: result.lottery,
      numbers: JSON.parse(result.winning_numbers),
      bonusNumbers: JSON.parse(result.bonus_numbers),
      date: result.draw_date,
      poolMoney: `$${result.total_pool_money}`
    }));
    
    res.json(formattedResults);
  } catch (error) {
    console.error('Error fetching results:', error);
    res.json([]);
  }
});

// Submit lottery entry (login required)
app.post('/api/play', authenticateToken, async (req, res) => {
  const { lottery, numbers, bonusNumbers, drawDate } = req.body;
  
  try {
    // Validate input
    if (!lottery || !numbers || !bonusNumbers || !drawDate) {
      return res.status(400).json({ error: 'Missing required fields' });
    }
    
    if (numbers.length !== 5 || bonusNumbers.length !== 2) {
      return res.status(400).json({ error: 'Invalid number selection' });
    }
    
    // Convert lottery code to full name
    const lotteryName = lottery === 'mon' ? 'Mon Lotto' : 
                       lottery === 'wed' ? 'Wed Lotto' : 'Fri Lotto';
    
    // Insert entry into database
    const [result] = await db.execute(
      'INSERT INTO entries (user_id, lottery, numbers, bonus_numbers, draw_date) VALUES (?, ?, ?, ?, ?)',
      [req.user.id, lotteryName, JSON.stringify(numbers), JSON.stringify(bonusNumbers), drawDate]
    );
    
    // Calculate new pool amount for this specific draw
    const [poolData] = await db.execute(
      'SELECT COUNT(*) * 0.001 as pool_amount FROM entries WHERE lottery = ? AND draw_date = ?',
      [lotteryName, drawDate]
    );
    
    const newPoolAmount = parseFloat(poolData[0].pool_amount) || 0.001;
    
    res.json({ 
      success: true, 
      message: 'Entry submitted successfully!',
      newPoolAmount: newPoolAmount.toFixed(3),
      entryId: result.insertId
    });
  } catch (error) {
    console.error('Entry submission error:', error);
    res.status(500).json({ error: 'Failed to submit entry' });
  }
});

// Get all entries
app.get('/api/entries', async (req, res) => {
  try {
    const [entries] = await db.execute(
      'SELECT e.*, u.email FROM entries e JOIN users u ON e.user_id = u.id ORDER BY e.created_at DESC'
    );
    res.json(entries);
  } catch (error) {
    console.error('Error fetching entries:', error);
    res.status(500).json({ error: 'Failed to fetch entries' });
  }
});

// Create lottery result (admin function)
app.post('/api/results', async (req, res) => {
  const { lottery, winningNumbers, bonusNumbers, drawDate } = req.body;
  
  try {
    // Calculate total pool money for this lottery
    const [poolResult] = await db.execute(
      'SELECT COUNT(*) * 0.001 as pool_amount FROM entries WHERE lottery = ?',
      [lottery]
    );
    
    const totalPool = poolResult[0].pool_amount || 0;
    
    // Store the result
    await db.execute(`
      INSERT INTO results (lottery, winning_numbers, bonus_numbers, draw_date, total_pool_money)
      VALUES (?, ?, ?, ?, ?)
    `, [lottery, JSON.stringify(winningNumbers), JSON.stringify(bonusNumbers), drawDate, totalPool]);
    
    res.json({ success: true, message: 'Result saved successfully' });
  } catch (error) {
    console.error('Error saving result:', error);
    res.status(500).json({ error: 'Failed to save result' });
  }
});

// Get pool money
app.get('/api/pool', async (req, res) => {
  try {
    const [poolData] = await db.execute(`
      SELECT lottery, COUNT(*) * 0.001 as pool_amount 
      FROM entries 
      GROUP BY lottery
    `);
    
    const pools = {
      'Mon Lotto': 0,
      'Wed Lotto': 0,
      'Fri Lotto': 0
    };
    
    poolData.forEach(row => {
      pools[row.lottery] = row.pool_amount;
    });
    
    res.json(pools);
  } catch (error) {
    console.error('Error fetching pool data:', error);
    res.json({ 'Mon Lotto': 0, 'Wed Lotto': 0, 'Fri Lotto': 0 });
  }
});

// Get statistics for About page
app.get('/api/stats', async (req, res) => {
  try {
    // Get total entries
    const [totalEntries] = await db.execute('SELECT COUNT(*) as total FROM entries');
    

    const [totalPayouts] = await db.execute('SELECT SUM(total_pool_money) as total FROM results');
    
    const [winnersLastMonth] = await db.execute(`
      SELECT COUNT(*) as winners FROM winners 
      WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
    `);
    
    const stats = {
      winnersLastMonth: winnersLastMonth[0].winners || 0,
      totalEntries: totalEntries[0].total || 0,
      totalPayouts: totalPayouts[0].total || 0
    };
    
    res.json(stats);
  } catch (error) {
    console.error('Error fetching stats:', error);
    res.json({
      winnersLastMonth: 0,
      totalEntries: 0,
      totalPayouts: 0
    });
  }
});

app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});