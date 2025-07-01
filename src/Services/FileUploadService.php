<?php

namespace App\Services;

class FileUploadService {
    private array $allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
        'application/zip',
        'application/x-rar-compressed'
    ];
    
    private int $maxFileSize = 10485760; // 10MB
    
    public function upload(array $file, string $directory = 'uploads'): array {
        $this->validateFile($file);
        
        $uploadDir = __DIR__ . '/../../public/uploads/' . $directory . '/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filePath = $uploadDir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new \Exception('Erro ao mover o arquivo');
        }
        
        return [
            'original_name' => $file['name'],
            'file_path' => '/uploads/' . $directory . '/' . $filename,
            'file_size' => $file['size'],
            'mime_type' => $file['type']
        ];
    }
    
    private function validateFile(array $file): void {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Erro no upload do arquivo: ' . $this->getUploadErrorMessage($file['error']));
        }
        
        if ($file['size'] > $this->maxFileSize) {
            throw new \Exception('Arquivo muito grande. Tamanho máximo: ' . $this->formatBytes($this->maxFileSize));
        }
        
        if (!in_array($file['type'], $this->allowedTypes)) {
            throw new \Exception('Tipo de arquivo não permitido');
        }
        
        // Verificação adicional de segurança
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $this->allowedTypes)) {
            throw new \Exception('Tipo de arquivo não permitido (verificação de segurança)');
        }
    }
    
    private function getUploadErrorMessage(int $error): string {
        switch ($error) {
            case UPLOAD_ERR_INI_SIZE:
                return 'Arquivo excede o tamanho máximo permitido no servidor';
            case UPLOAD_ERR_FORM_SIZE:
                return 'Arquivo excede o tamanho máximo permitido no formulário';
            case UPLOAD_ERR_PARTIAL:
                return 'Upload foi feito parcialmente';
            case UPLOAD_ERR_NO_FILE:
                return 'Nenhum arquivo foi enviado';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Pasta temporária não encontrada';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Falha ao escrever arquivo no disco';
            case UPLOAD_ERR_EXTENSION:
                return 'Upload foi interrompido por extensão';
            default:
                return 'Erro desconhecido';
        }
    }
    
    private function formatBytes(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    public function delete(string $filePath): bool {
        $fullPath = __DIR__ . '/../../public' . $filePath;
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }
}
