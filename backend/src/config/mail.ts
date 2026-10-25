import nodemailer from 'nodemailer';

// Configuração do transportador de e-mail
const transporter = nodemailer.createTransport({
  host: process.env.SMTP_HOST || 'smtp.gmail.com',
  port: Number(process.env.SMTP_PORT) || 587,
  secure: false,
  auth: {
    user: process.env.SMTP_USER,
    pass: process.env.SMTP_PASS
  }
});

// Interface para as opções de e-mail
interface SendMailOptions {
  to: string;
  subject: string;
  text?: string;
  html?: string;
}

// Função para enviar e-mail
export async function sendMail({ to, subject, text, html }: SendMailOptions): Promise<void> {
  try {
    await transporter.sendMail({
      from: process.env.SMTP_USER,
      to,
      subject,
      text,
      html
    });
  } catch (error) {
    console.error('Erro ao enviar e-mail:', error);
    throw new Error('Falha ao enviar e-mail');
  }
}

export { transporter };