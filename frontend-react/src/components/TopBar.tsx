import React from 'react';

interface TopBarProps {
  theme: 'dark' | 'light';
  toggleTheme: () => void;
  toggleSidebar: () => void;
  onLogout: () => void;
  isSidebarOpen: boolean;
  className?: string;
}

const TopBar: React.FC<TopBarProps> = ({ 
  theme, 
  toggleTheme, 
  toggleSidebar, 
  onLogout, 
  isSidebarOpen 
}) => {
  const styles = {
    topBar: {
      position: 'fixed' as 'fixed',
      top: 0,
      left: 0,
      right: 0,
      height: '60px',
      backgroundColor: theme === 'dark' ? '#1e293b' : '#ffffff',
      borderBottom: `1px solid ${theme === 'dark' ? '#334155' : '#e2e8f0'}`,
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'space-between',
      padding: '0 20px',
      zIndex: 1001,
      boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1)',
      transition: 'all 0.3s ease'
    },
    leftSection: {
      display: 'flex',
      alignItems: 'center',
      gap: '15px'
    },
    menuButton: {
      background: 'none',
      border: 'none',
      color: theme === 'dark' ? '#ffffff' : '#334155',
      fontSize: '20px',
      cursor: 'pointer',
      padding: '8px',
      borderRadius: '6px',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      transition: 'all 0.2s ease',
      width: '40px',
      height: '40px'
    },
    logo: {
      fontSize: '18px',
      fontWeight: 'bold' as 'bold',
      color: theme === 'dark' ? '#ffffff' : '#334155',
      margin: 0
    },
    rightSection: {
      display: 'flex',
      alignItems: 'center',
      gap: '10px'
    },
    themeButton: {
      background: 'none',
      border: `1px solid ${theme === 'dark' ? '#475569' : '#d1d5db'}`,
      color: theme === 'dark' ? '#ffffff' : '#374151',
      borderRadius: '6px',
      padding: '8px 12px',
      cursor: 'pointer',
      fontSize: '14px',
      display: 'flex',
      alignItems: 'center',
      gap: '6px',
      transition: 'all 0.2s ease',
      fontWeight: '500' as '500'
    },
    logoutButton: {
      background: '#dc2626',
      color: 'white',
      border: 'none',
      borderRadius: '6px',
      padding: '8px 16px',
      cursor: 'pointer',
      fontSize: '14px',
      fontWeight: '500' as '500',
      transition: 'all 0.2s ease',
      display: 'flex',
      alignItems: 'center',
      gap: '6px'
    }
  };

  return (
    <div style={styles.topBar}>
      <div style={styles.leftSection}>
        <button
          style={styles.menuButton}
          onClick={toggleSidebar}
          title={isSidebarOpen ? 'Fechar menu' : 'Abrir menu'}
          onMouseEnter={(e) => {
            e.currentTarget.style.backgroundColor = theme === 'dark' ? '#334155' : '#f1f5f9';
          }}
          onMouseLeave={(e) => {
            e.currentTarget.style.backgroundColor = 'transparent';
          }}
        >
          {isSidebarOpen ? '✕' : '☰'}
        </button>
        <h1 style={styles.logo}>Sistema de Relatórios</h1>
      </div>
      
      <div style={styles.rightSection}>
        <button
          style={styles.themeButton}
          onClick={toggleTheme}
          title={theme === 'dark' ? 'Modo claro' : 'Modo escuro'}
          onMouseEnter={(e) => {
            e.currentTarget.style.backgroundColor = theme === 'dark' ? '#334155' : '#f9fafb';
            e.currentTarget.style.borderColor = theme === 'dark' ? '#64748b' : '#9ca3af';
          }}
          onMouseLeave={(e) => {
            e.currentTarget.style.backgroundColor = 'transparent';
            e.currentTarget.style.borderColor = theme === 'dark' ? '#475569' : '#d1d5db';
          }}
        >
          {theme === 'dark' ? '☀️' : '🌙'}
          {theme === 'dark' ? 'Claro' : 'Escuro'}
        </button>
        
        <button
          style={styles.logoutButton}
          onClick={onLogout}
          title="Sair do sistema"
          onMouseEnter={(e) => {
            e.currentTarget.style.backgroundColor = '#b91c1c';
            e.currentTarget.style.transform = 'translateY(-1px)';
          }}
          onMouseLeave={(e) => {
            e.currentTarget.style.backgroundColor = '#dc2626';
            e.currentTarget.style.transform = 'translateY(0)';
          }}
        >
          🚪 Sair
        </button>
      </div>
    </div>
  );
};

export default TopBar;