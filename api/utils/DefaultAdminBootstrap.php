<?php

class DefaultAdminBootstrap
{
    private static bool $bootstrapped = false;

    public static function ensure(): void
    {
        if (self::$bootstrapped) {
            return;
        }

        self::$bootstrapped = true;

        try {
            $config = self::config();
            $pdo = Connection::get();

            $existingUser = self::findExistingUser($pdo, $config);

            if (!$existingUser) {
                self::createAdmin($pdo, $config);
                Logger::info('Default admin created', [
                    'email' => $config['email'],
                    'phone' => $config['phone'],
                ]);
                return;
            }

            if (self::needsUpdate($existingUser, $config)) {
                self::promoteExistingUser($pdo, (int)$existingUser['id'], $config);
                Logger::info('Default admin synchronized', [
                    'user_id' => $existingUser['id'],
                    'email' => $config['email'],
                ]);
            }
        } catch (Exception $e) {
            Logger::error('Default admin bootstrap failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private static function config(): array
    {
        return [
            'first_name' => Env::get('DEFAULT_ADMIN_FIRST_NAME', 'Lebohang'),
            'last_name' => Env::get('DEFAULT_ADMIN_LAST_NAME', 'Monamane'),
            'email' => Env::get('DEFAULT_ADMIN_EMAIL', 'monamane.lebohang45@gmail.com'),
            'phone' => Env::get('DEFAULT_ADMIN_PHONE', '26663274567'),
            'country' => Env::get('DEFAULT_ADMIN_COUNTRY', 'Lesotho'),
            'password' => Env::get('DEFAULT_ADMIN_PASSWORD', 'password'),
        ];
    }

    private static function findExistingUser(PDO $pdo, array $config): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM user WHERE email = :email LIMIT 1');
        $stmt->execute([
            ':email' => $config['email'],
        ]);

        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            return $existingUser;
        }

        $stmt = $pdo->prepare('SELECT * FROM user WHERE phone = :phone LIMIT 1');
        $stmt->execute([
            ':phone' => $config['phone'],
        ]);

        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        return $existingUser ?: null;
    }

    private static function createAdmin(PDO $pdo, array $config): void
    {
        $stmt = $pdo->prepare(
            'INSERT INTO user (
                first_name,
                last_name,
                email,
                phone,
                country,
                password,
                role,
                is_active,
                created_at
            ) VALUES (
                :first_name,
                :last_name,
                :email,
                :phone,
                :country,
                :password,
                :role,
                :is_active,
                NOW()
            )'
        );

        $stmt->execute([
            ':first_name' => $config['first_name'],
            ':last_name' => $config['last_name'],
            ':email' => $config['email'],
            ':phone' => $config['phone'],
            ':country' => $config['country'],
            ':password' => Hash::make($config['password']),
            ':role' => ROLE_ADMIN,
            ':is_active' => 1,
        ]);
    }

    private static function promoteExistingUser(PDO $pdo, int $userId, array $config): void
    {
        $passwordHash = Hash::make($config['password']);

        $stmt = $pdo->prepare(
            'UPDATE user
             SET first_name = :first_name,
                 last_name = :last_name,
                 email = :email,
                 phone = :phone,
                 country = :country,
                 password = :password,
                 role = :role,
                 is_active = :is_active,
                 updated_at = NOW()
             WHERE id = :id'
        );

        $stmt->execute([
            ':id' => $userId,
            ':first_name' => $config['first_name'],
            ':last_name' => $config['last_name'],
            ':email' => $config['email'],
            ':phone' => $config['phone'],
            ':country' => $config['country'],
            ':password' => $passwordHash,
            ':role' => ROLE_ADMIN,
            ':is_active' => 1,
        ]);
    }

    private static function needsUpdate(array $existingUser, array $config): bool
    {
        return $existingUser['first_name'] !== $config['first_name']
            || $existingUser['last_name'] !== $config['last_name']
            || $existingUser['email'] !== $config['email']
            || $existingUser['phone'] !== $config['phone']
            || $existingUser['country'] !== $config['country']
            || !Hash::check($config['password'], $existingUser['password'])
            || $existingUser['role'] !== ROLE_ADMIN
            || (int)$existingUser['is_active'] !== 1;
    }
}
