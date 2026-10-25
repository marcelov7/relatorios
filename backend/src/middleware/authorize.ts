import { Request, Response, NextFunction } from 'express';

export const authorize = (roles: string[]) => {
  return (req: Request, res: Response, next: NextFunction) => {
    try {
      if (!req.user) {
        return res.status(401).json({ message: 'Usuário não autenticado' });
      }

      if (!roles.includes(req.user.role)) {
        return res.status(403).json({ message: 'Permissão insuficiente' });
      }

      next();
    } catch (error) {
      console.error('Erro na autorização:', error);
      return res.status(500).json({ message: 'Erro interno do servidor' });
    }
  };
};