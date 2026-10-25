import React, { useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';

interface SidebarProps {
  isOpen: boolean;
  toggleSidebar: () => void;
  className?: string;
}

const Sidebar: React.FC<SidebarProps> = ({ isOpen, toggleSidebar }) => {
  const navigate = useNavigate();
  const location = useLocation();
  
  // Simulando dados do usuário (em um app real, viria do contexto/estado global)
  const user = {
    name: 'Administrador',
    isLoggedIn: true
  };

  const menuSections = {
    highlights: [
      { title: 'Dashboard', path: '/dashboard', icon: '📊', description: 'Visão geral do sistema' },
      { title: 'Novo Relatório', path: '/relatorios/novo', icon: '➕', description: 'Criar novo relatório' },
      { title: 'Relatórios Recentes', path: '/relatorios/recentes', icon: '🕒', description: 'Últimos relatórios' }
    ],
    management: [
      { title: 'Todos os Relatórios', path: '/relatorios', icon: '📝' },
      { title: 'Motores', path: '/motores', icon: '⚙️' },
      { title: 'Equipamentos', path: '/equipamentos', icon: '🔧' },
      { title: 'Locais', path: '/locais', icon: '📍' },
      { title: 'Setores', path: '/setores', icon: '🏢' }
    ],
    admin: [
      { title: 'Usuários', path: '/usuarios', icon: '👥' },
      { title: 'Configurações', path: '/configuracoes', icon: '⚙️' },
      { title: 'Relatórios do Sistema', path: '/sistema/relatorios', icon: '📈' }
    ]
  };

  const handleNavigation = (path: string) => {
    navigate(path);
    if (window.innerWidth < 768) {
      toggleSidebar();
    }
  };

  const styles = {
    sidebar: {
      position: 'fixed' as 'fixed',
      top: '60px', // Começar abaixo da TopBar
      left: 0,
      height: 'calc(100vh - 60px)', // Altura ajustada para a TopBar
      width: isOpen ? '280px' : '0px',
      backgroundColor: '#232f3e',
      transition: 'width 0.3s ease',
      overflowX: 'hidden' as 'hidden',
      overflowY: 'auto' as 'auto',
      zIndex: 1000,
      boxShadow: isOpen ? '2px 0 10px rgba(0,0,0,0.3)' : 'none'
    },
    userHeader: {
      padding: '16px 20px',
      backgroundColor: '#37475a',
      color: 'white',
      display: 'flex',
      justifyContent: 'space-between',
      alignItems: 'center',
      borderBottom: '1px solid #485769'
    },
    userInfo: {
      display: 'flex',
      alignItems: 'center',
      flex: 1
    },
    userAvatar: {
      width: '32px',
      height: '32px',
      borderRadius: '50%',
      backgroundColor: '#ff9900',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      marginRight: '12px',
      fontSize: '14px',
      fontWeight: 'bold' as 'bold',
      color: 'white'
    },
    userGreeting: {
      fontSize: '14px',
      fontWeight: 'bold' as 'bold',
      color: 'white'
    },
    closeButton: {
      background: 'none',
      border: 'none',
      color: '#aab7c4',
      fontSize: '20px',
      cursor: 'pointer',
      padding: '4px',
      borderRadius: '4px',
      transition: 'color 0.2s ease'
    },
    menuContainer: {
      padding: '0'
    },
    sectionTitle: {
      padding: '16px 20px 8px 20px',
      fontSize: '16px',
      fontWeight: 'bold' as 'bold',
      color: 'white',
      borderBottom: '1px solid #485769',
      margin: 0
    },
    menuList: {
      listStyle: 'none',
      padding: 0,
      margin: 0,
      borderBottom: '1px solid #485769'
    },
    menuItem: {
      padding: '12px 20px',
      color: '#ddd',
      display: 'flex',
      alignItems: 'center',
      cursor: 'pointer',
      transition: 'all 0.2s ease',
      borderLeft: '3px solid transparent'
    },
    menuItemHighlight: {
      padding: '16px 20px',
      color: '#ddd',
      display: 'flex',
      alignItems: 'flex-start',
      cursor: 'pointer',
      transition: 'all 0.2s ease',
      borderLeft: '3px solid transparent',
      flexDirection: 'column' as 'column'
    },
    activeMenuItem: {
      backgroundColor: '#485769',
      borderLeft: '3px solid #ff9900',
      color: 'white'
    },
    menuIcon: {
      marginRight: '12px',
      fontSize: '16px',
      width: '20px',
      textAlign: 'center' as 'center'
    },
    menuText: {
      fontSize: '14px',
      fontWeight: '500' as '500'
    },
    menuDescription: {
      fontSize: '12px',
      color: '#aab7c4',
      marginTop: '4px',
      marginLeft: '32px'
    }
  };

  const renderMenuItem = (item: any, isHighlight = false) => {
    const isActive = location.pathname === item.path;
    const itemStyle = isHighlight ? styles.menuItemHighlight : styles.menuItem;
    
    return (
      <li
        key={item.path}
        style={{
          ...itemStyle,
          ...(isActive ? styles.activeMenuItem : {})
        }}
        onClick={() => handleNavigation(item.path)}
        onMouseEnter={(e) => {
          if (!isActive) {
            e.currentTarget.style.backgroundColor = '#485769';
            e.currentTarget.style.color = 'white';
          }
        }}
        onMouseLeave={(e) => {
          if (!isActive) {
            e.currentTarget.style.backgroundColor = 'transparent';
            e.currentTarget.style.color = '#ddd';
          }
        }}
      >
        <div style={{ display: 'flex', alignItems: 'center', width: '100%' }}>
          <span style={styles.menuIcon}>{item.icon}</span>
          <span style={styles.menuText}>{item.title}</span>
        </div>
        {isHighlight && item.description && (
          <span style={styles.menuDescription}>{item.description}</span>
        )}
      </li>
    );
  };

  return (
    <div style={styles.sidebar}>
      {/* Cabeçalho do usuário */}
      <div style={styles.userHeader}>
        <div style={styles.userInfo}>
          <div style={styles.userAvatar}>
            {user.name.charAt(0).toUpperCase()}
          </div>
          <div style={styles.userGreeting}>
            Olá, {user.name}
          </div>
        </div>
        <button 
          onClick={toggleSidebar} 
          style={styles.closeButton}
          onMouseEnter={(e) => {
            e.currentTarget.style.color = 'white';
          }}
          onMouseLeave={(e) => {
            e.currentTarget.style.color = '#aab7c4';
          }}
        >
          ×
        </button>
      </div>
      
      <div style={styles.menuContainer}>
        {/* Seção Destaques */}
        <div style={styles.sectionTitle}>Destaques</div>
        <ul style={styles.menuList}>
          {menuSections.highlights.map((item) => renderMenuItem(item, true))}
        </ul>

        {/* Seção Gerenciamento */}
        <div style={styles.sectionTitle}>Gerenciamento</div>
        <ul style={styles.menuList}>
          {menuSections.management.map((item) => renderMenuItem(item))}
        </ul>

        {/* Seção Administração */}
        <div style={styles.sectionTitle}>Administração</div>
        <ul style={styles.menuList}>
          {menuSections.admin.map((item) => renderMenuItem(item))}
        </ul>
      </div>
    </div>
  );
};

export default Sidebar;