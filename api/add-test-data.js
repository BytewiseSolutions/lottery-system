const mysql = require('mysql2/promise');
require('dotenv').config();

async function addTestData() {
    try {
        const connection = await mysql.createConnection({
            host: process.env.DB_HOST || 'localhost',
            user: process.env.DB_USER || 'root',
            password: process.env.DB_PASSWORD || '',
            database: process.env.DB_NAME || 'lottery_db'
        });

        // Add test user
        await connection.execute(`
            INSERT IGNORE INTO users (id, email, password) VALUES (1, 'test@example.com', 'hashedpassword')
        `);

        // Add test entries
        await connection.execute(`
            INSERT IGNORE INTO entries (user_id, lottery, numbers, bonus_numbers) VALUES 
            (1, 'Mon Lotto', '[1,2,3,4,5]', '[10,20]'),
            (1, 'Wed Lotto', '[6,7,8,9,10]', '[30,40]'),
            (1, 'Fri Lotto', '[11,12,13,14,15]', '[50,60]')
        `);

        // Add test results
        await connection.execute(`
            INSERT IGNORE INTO results (lottery, winning_numbers, bonus_numbers, draw_date, total_pool_money) VALUES 
            ('Mon Lotto', '[1,2,3,4,5]', '[10,20]', '2024-01-15', 500.00),
            ('Wed Lotto', '[6,7,8,9,10]', '[30,40]', '2024-01-17', 750.00)
        `);

        await connection.end();
        console.log('Test data added successfully!');
    } catch (error) {
        console.error('Error adding test data:', error.message);
    }
}

addTestData();