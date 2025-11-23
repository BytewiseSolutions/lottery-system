const mysql = require('mysql2/promise');
require('dotenv').config();

async function removeTestUsers() {
  try {
    const connection = await mysql.createConnection({
      host: process.env.DB_HOST || 'localhost',
      user: process.env.DB_USER || 'root',
      password: process.env.DB_PASSWORD || '',
      database: process.env.DB_NAME || 'lottery_db'
    });

    // Remove test users (assuming test emails contain 'test' or common test patterns)
    const [result] = await connection.execute(`
      DELETE FROM users 
      WHERE email LIKE '%test%' 
      OR email LIKE '%example%' 
      OR email LIKE '%demo%'
      OR email = 'user@test.com'
      OR email = 'test@test.com'
    `);

    console.log(`Removed ${result.affectedRows} test user(s)`);
    
    await connection.end();
  } catch (error) {
    console.error('Error removing test users:', error);
  }
}

removeTestUsers();