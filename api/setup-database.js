const mysql = require('mysql2/promise');
require('dotenv').config();

async function setupDatabase() {
    try {
        const connection = await mysql.createConnection({
            host: process.env.DB_HOST || 'localhost',
            user: process.env.DB_USER || 'root',
            password: process.env.DB_PASSWORD || ''
        });

        console.log('Connected to MySQL server');

        await connection.execute(`CREATE DATABASE IF NOT EXISTS ${process.env.DB_NAME || 'lottery_db'}`);
        console.log(`Database '${process.env.DB_NAME || 'lottery_db'}' created or already exists`);

        await connection.end();

        const dbConnection = await mysql.createConnection({
            host: process.env.DB_HOST || 'localhost',
            user: process.env.DB_USER || 'root',
            password: process.env.DB_PASSWORD || '',
            database: process.env.DB_NAME || 'lottery_db'
        });

        await dbConnection.execute(`
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        `);
        console.log('Users table created or already exists');

        await dbConnection.execute(`DROP TABLE IF EXISTS entries`);
        await dbConnection.execute(`
            CREATE TABLE entries (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                lottery VARCHAR(50) NOT NULL,
                numbers JSON NOT NULL,
                bonus_numbers JSON NOT NULL,
                draw_date DATE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )
        `);
        console.log('Entries table created or already exists');

        await dbConnection.execute(`
            CREATE TABLE IF NOT EXISTS results (
                id INT AUTO_INCREMENT PRIMARY KEY,
                lottery VARCHAR(50) NOT NULL,
                winning_numbers JSON NOT NULL,
                bonus_numbers JSON NOT NULL,
                draw_date DATE NOT NULL,
                total_pool_money DECIMAL(10,3) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        `);
        console.log('Results table created or already exists');

        await dbConnection.execute(`
            CREATE TABLE IF NOT EXISTS winners (
                id INT AUTO_INCREMENT PRIMARY KEY,
                entry_id INT,
                result_id INT,
                user_id INT,
                prize_amount DECIMAL(10,3),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (entry_id) REFERENCES entries(id),
                FOREIGN KEY (result_id) REFERENCES results(id),
                FOREIGN KEY (user_id) REFERENCES users(id)
            )
        `);
        console.log('Winners table created or already exists');

        await dbConnection.end();
        console.log('Database setup completed successfully!');

    } catch (error) {
        console.error('Database setup failed:', error.message);
        process.exit(1);
    }
}

setupDatabase();