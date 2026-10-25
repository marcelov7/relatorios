import { validateUser, validateReport, validateEquipment, validateSector } from '../utils/validation';
import { ValidationError } from '../errors/ValidationError';

describe('Validation System', () => {
  describe('User Validation', () => {
    it('should validate valid user data', () => {
      const userData = {
        name: 'John Doe',
        email: 'john@example.com',
        password: 'StrongPass123!',
        role: 'USER'
      };

      expect(() => validateUser(userData)).not.toThrow();
    });

    it('should validate email format', () => {
      const userData = {
        name: 'John Doe',
        email: 'invalid-email',
        password: 'StrongPass123!',
        role: 'USER'
      };

      expect(() => validateUser(userData)).toThrow(ValidationError);
    });

    it('should validate password strength', () => {
      const userData = {
        name: 'John Doe',
        email: 'john@example.com',
        password: 'weak',
        role: 'USER'
      };

      expect(() => validateUser(userData)).toThrow(ValidationError);
    });
  });

  describe('Report Validation', () => {
    it('should validate valid report data', () => {
      const reportData = {
        title: 'Test Report',
        description: 'Test Description',
        status: 'PENDING',
        priority: 'MEDIUM',
        equipmentId: 1,
        sectorId: 1
      };

      expect(() => validateReport(reportData)).not.toThrow();
    });

    it('should validate status enum', () => {
      const reportData = {
        title: 'Test Report',
        description: 'Test Description',
        status: 'INVALID_STATUS',
        priority: 'MEDIUM',
        equipmentId: 1,
        sectorId: 1
      };

      expect(() => validateReport(reportData)).toThrow(ValidationError);
    });

    it('should validate required fields', () => {
      const reportData = {
        title: 'Test Report',
        status: 'PENDING'
      };

      expect(() => validateReport(reportData)).toThrow(ValidationError);
    });
  });

  describe('Equipment Validation', () => {
    it('should validate valid equipment data', () => {
      const equipmentData = {
        name: 'Test Equipment',
        model: 'Test Model',
        serialNumber: '123456',
        manufacturer: 'Test Manufacturer',
        sectorId: 1
      };

      expect(() => validateEquipment(equipmentData)).not.toThrow();
    });

    it('should validate serial number format', () => {
      const equipmentData = {
        name: 'Test Equipment',
        model: 'Test Model',
        serialNumber: 'invalid#serial',
        manufacturer: 'Test Manufacturer',
        sectorId: 1
      };

      expect(() => validateEquipment(equipmentData)).toThrow(ValidationError);
    });

    it('should validate required fields', () => {
      const equipmentData = {
        name: 'Test Equipment',
        model: 'Test Model'
      };

      expect(() => validateEquipment(equipmentData)).toThrow(ValidationError);
    });
  });

  describe('Sector Validation', () => {
    it('should validate valid sector data', () => {
      const sectorData = {
        name: 'Test Sector',
        description: 'Test Description',
        location: 'Building A, Floor 1'
      };

      expect(() => validateSector(sectorData)).not.toThrow();
    });

    it('should validate required fields', () => {
      const sectorData = {
        description: 'Test Description'
      };

      expect(() => validateSector(sectorData)).toThrow(ValidationError);
    });

    it('should validate location format', () => {
      const sectorData = {
        name: 'Test Sector',
        description: 'Test Description',
        location: 'Invalid@Location#'
      };

      expect(() => validateSector(sectorData)).toThrow(ValidationError);
    });
  });

  describe('Error Messages', () => {
    it('should provide clear error message for invalid email', () => {
      try {
        validateUser({
          name: 'John Doe',
          email: 'invalid-email',
          password: 'StrongPass123!',
          role: 'USER'
        });
      } catch (error) {
        expect(error.message).toContain('email');
      }
    });

    it('should provide clear error message for missing fields', () => {
      try {
        validateReport({
          title: 'Test Report'
        });
      } catch (error) {
        expect(error.message).toContain('required');
      }
    });
  });