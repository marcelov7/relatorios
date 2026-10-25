import { sendEmail, sendTemplateEmail, EmailTemplate } from '../services/email';
import nodemailer from 'nodemailer';
import { SMTP } from '../config/env';

// Mock do nodemailer
jest.mock('nodemailer');

describe('Email Service', () => {
  let mockTransporter: any;

  beforeEach(() => {
    // Limpar todos os mocks
    jest.clearAllMocks();

    // Configurar mock do transportador
    mockTransporter = {
      sendMail: jest.fn().mockResolvedValue({
        messageId: 'test-message-id',
        response: 'test-response'
      })
    };

    (nodemailer.createTransport as jest.Mock).mockReturnValue(mockTransporter);
  });

  describe('Basic Email Sending', () => {
    it('should send simple email successfully', async () => {
      const emailData = {
        to: 'test@example.com',
        subject: 'Test Email',
        text: 'This is a test email'
      };

      const result = await sendEmail(emailData);

      expect(result).toBeTruthy();
      expect(mockTransporter.sendMail).toHaveBeenCalledWith(
        expect.objectContaining({
          from: SMTP.USER,
          to: emailData.to,
          subject: emailData.subject,
          text: emailData.text
        })
      );
    });

    it('should send HTML email successfully', async () => {
      const emailData = {
        to: 'test@example.com',
        subject: 'Test HTML Email',
        html: '<h1>Test</h1><p>This is a test email</p>'
      };

      const result = await sendEmail(emailData);

      expect(result).toBeTruthy();
      expect(mockTransporter.sendMail).toHaveBeenCalledWith(
        expect.objectContaining({
          html: emailData.html
        })
      );
    });

    it('should handle multiple recipients', async () => {
      const emailData = {
        to: ['test1@example.com', 'test2@example.com'],
        subject: 'Test Multiple Recipients',
        text: 'This is a test email'
      };

      const result = await sendEmail(emailData);

      expect(result).toBeTruthy();
      expect(mockTransporter.sendMail).toHaveBeenCalledWith(
        expect.objectContaining({
          to: emailData.to
        })
      );
    });
  });

  describe('Template Emails', () => {
    it('should send welcome email template', async () => {
      const templateData = {
        name: 'John Doe',
        confirmationLink: 'http://example.com/confirm'
      };

      const result = await sendTemplateEmail(
        'test@example.com',
        EmailTemplate.WELCOME,
        templateData
      );

      expect(result).toBeTruthy();
      expect(mockTransporter.sendMail).toHaveBeenCalledWith(
        expect.objectContaining({
          html: expect.stringContaining(templateData.name)
        })
      );
    });

    it('should send password reset template', async () => {
      const templateData = {
        resetLink: 'http://example.com/reset',
        expirationTime: '1 hour'
      };

      const result = await sendTemplateEmail(
        'test@example.com',
        EmailTemplate.PASSWORD_RESET,
        templateData
      );

      expect(result).toBeTruthy();
      expect(mockTransporter.sendMail).toHaveBeenCalledWith(
        expect.objectContaining({
          html: expect.stringContaining(templateData.resetLink)
        })
      );
    });

    it('should send report notification template', async () => {
      const templateData = {
        reportId: '123',
        reportTitle: 'Test Report',
        status: 'COMPLETED',
        viewLink: 'http://example.com/report/123'
      };

      const result = await sendTemplateEmail(
        'test@example.com',
        EmailTemplate.REPORT_NOTIFICATION,
        templateData
      );

      expect(result).toBeTruthy();
      expect(mockTransporter.sendMail).toHaveBeenCalledWith(
        expect.objectContaining({
          html: expect.stringContaining(templateData.reportTitle)
        })
      );
    });
  });

  describe('Email Validation', () => {
    it('should validate email address format', async () => {
      const invalidEmail = 'invalid-email';

      await expect(sendEmail({
        to: invalidEmail,
        subject: 'Test',
        text: 'Test'
      })).rejects.toThrow();
    });

    it('should require subject', async () => {
      await expect(sendEmail({
        to: 'test@example.com',
        text: 'Test'
      } as any)).rejects.toThrow();
    });

    it('should require either text or html content', async () => {
      await expect(sendEmail({
        to: 'test@example.com',
        subject: 'Test'
      } as any)).rejects.toThrow();
    });
  });

  describe('Template Validation', () => {
    it('should validate required template data', async () => {
      await expect(sendTemplateEmail(
        'test@example.com',
        EmailTemplate.WELCOME,
        {} as any
      )).rejects.toThrow();
    });

    it('should validate template type', async () => {
      await expect(sendTemplateEmail(
        'test@example.com',
        'INVALID_TEMPLATE' as EmailTemplate,
        {}
      )).rejects.toThrow();
    });
  });

  describe('Error Handling', () => {
    it('should handle SMTP connection errors', async () => {
      mockTransporter.sendMail.mockRejectedValue(new Error('SMTP Error'));

      await expect(sendEmail({
        to: 'test@example.com',
        subject: 'Test',
        text: 'Test'
      })).rejects.toThrow('SMTP Error');
    });

    it('should handle template rendering errors', async () => {
      await expect(sendTemplateEmail(
        'test@example.com',
        EmailTemplate.WELCOME,
        { invalidData: true } as any
      )).rejects.toThrow();
    });

    it('should handle invalid SMTP configuration', async () => {
      (nodemailer.createTransport as jest.Mock).mockImplementation(() => {
        throw new Error('Invalid config');
      });

      await expect(sendEmail({
        to: 'test@example.com',
        subject: 'Test',
        text: 'Test'
      })).rejects.toThrow();
    });
  });

  describe('Email Attachments', () => {
    it('should send email with file attachment', async () => {
      const emailData = {
        to: 'test@example.com',
        subject: 'Test with Attachment',
        text: 'This is a test email',
        attachments: [{
          filename: 'test.txt',
          content: 'Hello World'
        }]
      };

      const result = await sendEmail(emailData);

      expect(result).toBeTruthy();
      expect(mockTransporter.sendMail).toHaveBeenCalledWith(
        expect.objectContaining({
          attachments: emailData.attachments
        })
      );
    });

    it('should send email with multiple attachments', async () => {
      const emailData = {
        to: 'test@example.com',
        subject: 'Test with Multiple Attachments',
        text: 'This is a test email',
        attachments: [
          { filename: 'test1.txt', content: 'Content 1' },
          { filename: 'test2.txt', content: 'Content 2' }
        ]
      };

      const result = await sendEmail(emailData);

      expect(result).toBeTruthy();
      expect(mockTransporter.sendMail).toHaveBeenCalledWith(
        expect.objectContaining({
          attachments: expect.arrayContaining([
            expect.objectContaining({ filename: 'test1.txt' }),
            expect.objectContaining({ filename: 'test2.txt' })
          ])
        })
      );
    });

    it('should validate attachment size', async () => {
      const largeAttachment = {
        filename: 'large.txt',
        content: Buffer.alloc(10 * 1024 * 1024) // 10MB
      };

      await expect(sendEmail({
        to: 'test@example.com',
        subject: 'Test Large Attachment',
        text: 'Test',
        attachments: [largeAttachment]
      })).rejects.toThrow();
    });
  });

  describe('Email Queue', () => {
    it('should handle email queue', async () => {
      const emails = Array.from({ length: 5 }, (_, i) => ({
        to: `test${i}@example.com`,
        subject: `Test Email ${i}`,
        text: `This is test email ${i}`
      }));

      const results = await Promise.all(emails.map(email => sendEmail(email)));

      expect(results).toHaveLength(emails.length);
      expect(mockTransporter.sendMail).toHaveBeenCalledTimes(emails.length);
    });

    it('should handle queue errors gracefully', async () => {
      mockTransporter.sendMail
        .mockResolvedValueOnce({ messageId: '1' })
        .mockRejectedValueOnce(new Error('Failed'))
        .mockResolvedValueOnce({ messageId: '3' });

      const emails = Array.from({ length: 3 }, (_, i) => ({
        to: `test${i}@example.com`,
        subject: `Test Email ${i}`,
        text: `This is test email ${i}`
      }));

      const results = await Promise.allSettled(emails.map(email => sendEmail(email)));

      expect(results).toHaveLength(3);
      expect(results[0].status).toBe('fulfilled');
      expect(results[1].status).toBe('rejected');
      expect(results[2].status).toBe('fulfilled');
    });
  });
});