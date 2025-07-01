<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private PHPMailer $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->configureMailer();
    }
    
    private function configureMailer(): void {
        $config = require __DIR__ . '/../../config/mail.php';
        
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = $config['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $config['username'];
            $this->mailer->Password = $config['password'];
            $this->mailer->SMTPSecure = $config['encryption'];
            $this->mailer->Port = $config['port'];
            $this->mailer->CharSet = 'UTF-8';
            
            $this->mailer->setFrom($config['from_address'], $config['from_name']);
        } catch (Exception $e) {
            throw new \Exception('Erro na configuração do email: ' . $e->getMessage());
        }
    }
    
    public function sendProjectNotification(string $to, string $projectTitle, string $status): bool {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = "Atualização do Projeto: {$projectTitle}";
            
            $statusMessages = [
                'pending' => 'foi criado e está aguardando análise',
                'in_progress' => 'está em andamento',
                'completed' => 'foi concluído',
                'cancelled' => 'foi cancelado'
            ];
            
            $message = $statusMessages[$status] ?? 'teve seu status atualizado';
            
            $this->mailer->Body = $this->getEmailTemplate([
                'title' => 'Atualização de Projeto',
                'message' => "O projeto \"{$projectTitle}\" {$message}.",
                'action_text' => 'Ver Projeto',
                'action_url' => $this->getBaseUrl() . '/projects'
            ]);
            
            $this->mailer->send();
            $this->mailer->clearAddresses();
            
            return true;
        } catch (Exception $e) {
            error_log('Erro ao enviar email: ' . $e->getMessage());
            return false;
        }
    }
    
    public function sendDocumentNotification(string $to, string $projectTitle, string $documentName): bool {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = "Novo Documento: {$projectTitle}";
            
            $this->mailer->Body = $this->getEmailTemplate([
                'title' => 'Novo Documento',
                'message' => "Um novo documento \"{$documentName}\" foi adicionado ao projeto \"{$projectTitle}\".",
                'action_text' => 'Ver Projeto',
                'action_url' => $this->getBaseUrl() . '/projects'
            ]);
            
            $this->mailer->send();
            $this->mailer->clearAddresses();
            
            return true;
        } catch (Exception $e) {
            error_log('Erro ao enviar email: ' . $e->getMessage());
            return false;
        }
    }
    
    public function sendWelcomeEmail(string $to, string $name): bool {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Bem-vindo ao Sistema de Arquitetura';
            
            $this->mailer->Body = $this->getEmailTemplate([
                'title' => 'Bem-vindo!',
                'message' => "Olá {$name}, sua conta foi criada com sucesso no Sistema de Arquitetura.",
                'action_text' => 'Acessar Sistema',
                'action_url' => $this->getBaseUrl() . '/login'
            ]);
            
            $this->mailer->send();
            $this->mailer->clearAddresses();
            
            return true;
        } catch (Exception $e) {
            error_log('Erro ao enviar email: ' . $e->getMessage());
            return false;
        }
    }
    
    private function getEmailTemplate(array $data): string {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$data['title']}</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .button { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>{$data['title']}</h1>
                </div>
                <div class='content'>
                    <p>{$data['message']}</p>
                    <p style='text-align: center; margin-top: 30px;'>
                        <a href='{$data['action_url']}' class='button'>{$data['action_text']}</a>
                    </p>
                </div>
                <div class='footer'>
                    <p>Sistema de Arquitetura - Gestão de Documentos e Projetos</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function getBaseUrl(): string {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
        return $protocol . '://' . $host;
    }
}
