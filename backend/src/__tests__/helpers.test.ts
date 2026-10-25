import {
  validateEmail,
  validatePassword,
  sanitizeHtml,
  formatDate,
  generateSlug,
  paginateResults,
  validateFields,
  formatCurrency,
  calculateDateDiff,
  maskSensitiveData
} from '../utils/helpers';

describe('Helper Functions', () => {
  describe('validateEmail', () => {
    it('should validate correct email formats', () => {
      const validEmails = [
        'test@example.com',
        'user.name@domain.com',
        'user+tag@domain.co.uk',
        'user123@subdomain.domain.com'
      ];

      validEmails.forEach(email => {
        expect(validateEmail(email)).toBe(true);
      });
    });

    it('should reject invalid email formats', () => {
      const invalidEmails = [
        'invalid.email',
        '@domain.com',
        'user@',
        'user@.com',
        'user@domain.',
        'user space@domain.com'
      ];

      invalidEmails.forEach(email => {
        expect(validateEmail(email)).toBe(false);
      });
    });
  });

  describe('validatePassword', () => {
    it('should validate strong passwords', () => {
      const validPasswords = [
        'StrongPass123!',
        'Complex@Password1',
        'Secure123#Password',
        'ValidP@ssw0rd'
      ];

      validPasswords.forEach(password => {
        expect(validatePassword(password)).toBe(true);
      });
    });

    it('should reject weak passwords', () => {
      const invalidPasswords = [
        'short1',           // muito curta
        'onlylowercase',    // sem maiúsculas
        'ONLYUPPERCASE',    // sem minúsculas
        'WithoutNumbers',   // sem números
        'no_special1',      // sem caracteres especiais
        '12345678'          // só números
      ];

      invalidPasswords.forEach(password => {
        expect(validatePassword(password)).toBe(false);
      });
    });
  });

  describe('sanitizeHtml', () => {
    it('should remove HTML tags', () => {
      const input = '<p>Test <script>alert("xss")</script> content</p>';
      expect(sanitizeHtml(input)).toBe('Test content');
    });

    it('should handle special characters', () => {
      const input = '&lt;div&gt;Test &amp; content&lt;/div&gt;';
      expect(sanitizeHtml(input)).toBe('<div>Test & content</div>');
    });

    it('should preserve line breaks', () => {
      const input = 'Line 1\\nLine 2\\nLine 3';
      const sanitized = sanitizeHtml(input);
      expect(sanitized.includes('\\n')).toBe(true);
    });
  });

  describe('formatDate', () => {
    it('should format date correctly', () => {
      const date = new Date('2024-03-15T10:30:00');
      expect(formatDate(date)).toBe('15/03/2024');
    });

    it('should handle different date formats', () => {
      const date = new Date('2024-03-15T10:30:00');
      expect(formatDate(date, 'dd/MM/yyyy HH:mm')).toBe('15/03/2024 10:30');
    });

    it('should handle invalid dates', () => {
      expect(formatDate(new Date('invalid'))).toBe('Data inválida');
    });
  });

  describe('generateSlug', () => {
    it('should generate valid slugs', () => {
      expect(generateSlug('Test Title')).toBe('test-title');
      expect(generateSlug('Complex: Title & Symbols!')).toBe('complex-title-symbols');
      expect(generateSlug('Acentuação e Caracteres Especiais')).toBe('acentuacao-e-caracteres-especiais');
    });

    it('should handle multiple spaces and special characters', () => {
      expect(generateSlug('  Multiple   Spaces  ')).toBe('multiple-spaces');
      expect(generateSlug('Special@#$%Characters')).toBe('special-characters');
    });

    it('should handle empty or invalid input', () => {
      expect(generateSlug('')).toBe('');
      expect(generateSlug('   ')).toBe('');
      expect(generateSlug('@#$%')).toBe('');
    });
  });

  describe('paginateResults', () => {
    const items = Array.from({ length: 50 }, (_, i) => ({ id: i + 1 }));

    it('should paginate results correctly', () => {
      const page1 = paginateResults(items, 1, 10);
      expect(page1.data).toHaveLength(10);
      expect(page1.data[0].id).toBe(1);
      expect(page1.totalPages).toBe(5);
      expect(page1.currentPage).toBe(1);
    });

    it('should handle last page', () => {
      const lastPage = paginateResults(items, 5, 10);
      expect(lastPage.data).toHaveLength(10);
      expect(lastPage.data[0].id).toBe(41);
      expect(lastPage.hasNextPage).toBe(false);
    });

    it('should handle invalid page numbers', () => {
      const invalidPage = paginateResults(items, -1, 10);
      expect(invalidPage.currentPage).toBe(1);

      const overflowPage = paginateResults(items, 10, 10);
      expect(overflowPage.currentPage).toBe(5);
    });
  });

  describe('validateFields', () => {
    const schema = {
      name: { required: true, type: 'string', minLength: 3 },
      age: { required: true, type: 'number', min: 18 },
      email: { required: true, type: 'string', validate: validateEmail }
    };

    it('should validate correct data', () => {
      const data = {
        name: 'John Doe',
        age: 25,
        email: 'john@example.com'
      };

      const result = validateFields(data, schema);
      expect(result.isValid).toBe(true);
      expect(result.errors).toHaveLength(0);
    });

    it('should detect missing required fields', () => {
      const data = {
        name: 'John Doe'
      };

      const result = validateFields(data, schema);
      expect(result.isValid).toBe(false);
      expect(result.errors).toContainEqual(
        expect.objectContaining({ field: 'age' })
      );
    });

    it('should validate field types', () => {
      const data = {
        name: 'John',
        age: '25', // deveria ser número
        email: 'john@example.com'
      };

      const result = validateFields(data, schema);
      expect(result.isValid).toBe(false);
      expect(result.errors).toContainEqual(
        expect.objectContaining({ field: 'age' })
      );
    });
  });

  describe('formatCurrency', () => {
    it('should format currency correctly', () => {
      expect(formatCurrency(1234.56)).toBe('R$ 1.234,56');
      expect(formatCurrency(1000000)).toBe('R$ 1.000.000,00');
      expect(formatCurrency(0.99)).toBe('R$ 0,99');
    });

    it('should handle negative values', () => {
      expect(formatCurrency(-1234.56)).toBe('-R$ 1.234,56');
    });

    it('should handle zero and undefined', () => {
      expect(formatCurrency(0)).toBe('R$ 0,00');
      expect(formatCurrency()).toBe('R$ 0,00');
    });
  });

  describe('calculateDateDiff', () => {
    it('should calculate difference in days', () => {
      const date1 = new Date('2024-03-15');
      const date2 = new Date('2024-03-20');
      expect(calculateDateDiff(date1, date2, 'days')).toBe(5);
    });

    it('should calculate difference in hours', () => {
      const date1 = new Date('2024-03-15T10:00:00');
      const date2 = new Date('2024-03-15T15:00:00');
      expect(calculateDateDiff(date1, date2, 'hours')).toBe(5);
    });

    it('should handle invalid dates', () => {
      expect(calculateDateDiff(new Date('invalid'), new Date(), 'days')).toBe(0);
    });
  });

  describe('maskSensitiveData', () => {
    it('should mask email addresses', () => {
      expect(maskSensitiveData('user@example.com', 'email'))
        .toBe('u***@example.com');
    });

    it('should mask phone numbers', () => {
      expect(maskSensitiveData('11999887766', 'phone'))
        .toBe('(11) *****-7766');
    });

    it('should mask document numbers', () => {
      expect(maskSensitiveData('12345678900', 'document'))
        .toBe('***.***.789-00');
    });

    it('should handle invalid input', () => {
      expect(maskSensitiveData('', 'email')).toBe('');
      expect(maskSensitiveData('invalid', 'phone')).toBe('invalid');
    });
  });
});