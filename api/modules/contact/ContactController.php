<?php

class ContactController
{
    private $contactService;

    public function __construct()
    {
        $this->contactService = new ContactService();
    }

    public function submit()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $result = $this->contactService->submit($input);

            if ($result['success']) {
                Response::json(true, $result['message'], $result['data'] ?? null, HTTP_CREATED);
            }

            $statusCode = isset($result['errors']) ? HTTP_UNPROCESSABLE_ENTITY : HTTP_BAD_REQUEST;
            $data = isset($result['errors']) ? ['errors' => $result['errors']] : null;
            Response::json(false, $result['message'], $data, $statusCode);
        } catch (Exception $e) {
            Logger::error('Contact submit failed', [
                'error' => $e->getMessage()
            ]);

            Response::json(false, 'Failed to send message', null, HTTP_INTERNAL_ERROR);
        }
    }
}
