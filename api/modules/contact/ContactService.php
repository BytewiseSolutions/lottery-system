<?php

class ContactService
{
    private $contactRepository;

    public function __construct()
    {
        $this->contactRepository = new ContactRepository();
    }

    public function submit(array $data)
    {
        $name = trim((string)($data['name'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $message = trim((string)($data['message'] ?? ''));

        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Name is required';
        }

        if ($email === '') {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email address is invalid';
        }

        if ($message === '') {
            $errors['message'] = 'Message is required';
        }

        if ($errors) {
            return [
                'success' => false,
                'message' => 'Please fix the highlighted errors',
                'errors' => $errors
            ];
        }

        $created = $this->contactRepository->create($name, $email, $message);

        if (!$created) {
            return [
                'success' => false,
                'message' => 'Failed to send message'
            ];
        }

        Logger::info('Contact message created', [
            'email' => $email,
            'name' => $name
        ]);

        return [
            'success' => true,
            'message' => 'Thank you! Your message has been sent successfully.',
            'data' => [
                'submitted' => true
            ]
        ];
    }
}
