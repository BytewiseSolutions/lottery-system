const mysql = require('mysql2/promise');
require('dotenv').config();

async function migrateEntries() {
    try {
        const connection = await mysql.createConnection({
            host: process.env.DB_HOST || 'localhost',
            user: process.env.DB_USER || 'root',
            password: process.env.DB_PASSWORD || '',
            database: process.env.DB_NAME || 'lottery_db'
        });

        // Add draw_date column if it doesn't exist
        await connection.execute(`
            ALTER TABLE entries 
            ADD COLUMN IF NOT EXISTS draw_date DATE NOT NULL DEFAULT '2024-12-01'
        `);
        
        console.log('Migration completed successfully!');
        await connection.end();
    } catch (error) {
        console.error('Migration failed:', error.message);
    }
}

migrateEntries();