import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { logout } from '../services/authService';
import Sidebar from '../components/Sidebar';
import TopBar from '../components/TopBar';

// Definição de temas
const themes = {
  dark: {
    backgroundColor: '#121826',
    cardBackground: '#1e293b',
    textColor: '#ffffff',
    borderColor: '#2a3447',
    headerBorder: '#2a3447'
  },
  light: {
    backgroundColor: '#f8fafc',
    cardBackground: '#ffffff',
    textColor: '#334155',
    borderColor: '#e2e8f0',
    headerBorder: '#e2e8f0'
  }
};

// Estilos inline para o dashboard
const getStyles = (theme: 'dark' | 'light') => {
  return {
  mainContainer: {
    display: 'flex',
    height: '100vh',
    width: '100%',
    overflow: 'hidden',
    position: 'relative' as 'relative',
    backgroundColor: themes[theme].backgroundColor,
    color: themes[theme].textColor,
    transition: 'all 0.3s ease'
  },
  content: {
    flex: 1,
    marginLeft: '250px',
    marginTop: '60px', // Espaço para a TopBar
    transition: 'margin-left 0.3s ease',
    overflowY: 'auto' as 'auto',
    height: 'calc(100vh - 60px)', // Altura ajustada para a TopBar
    backgroundColor: themes[theme].backgroundColor
  },
  contentCollapsed: {
    marginLeft: '0px'
  },
  container: {
    padding: '20px',
    maxWidth: '1200px',
    margin: '0 auto'
  },

  statsContainer: {
    display: 'grid',
    gridTemplateColumns: 'repeat(auto-fill, minmax(250px, 1fr))',
    gap: '20px',
    marginBottom: '30px'
  },
  statCard: {
    backgroundColor: themes[theme].cardBackground,
    borderRadius: '8px',
    padding: '20px',
    boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1)',
    display: 'flex',
    flexDirection: 'column' as 'column',
    alignItems: 'center',
    justifyContent: 'center',
    transition: 'transform 0.3s ease, box-shadow 0.3s ease',
    cursor: 'pointer',
    border: `1px solid ${themes[theme].borderColor}`
  },
  statIcon: {
    width: '40px',
    height: '40px',
    marginBottom: '10px',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: '50%'
  },
  statValue: {
    fontSize: '28px',
    fontWeight: 'bold',
    margin: '10px 0'
  },
  statTitle: {
    fontSize: '14px',
    color: themes[theme].textColor,
    opacity: 0.8
  },
  actionsContainer: {
    display: 'grid',
    gridTemplateColumns: 'repeat(auto-fill, minmax(300px, 1fr))',
    gap: '20px',
    marginBottom: '30px'
  },
  actionCard: {
    backgroundColor: themes[theme].cardBackground,
    borderRadius: '8px',
    padding: '20px',
    boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1)',
    display: 'flex',
    alignItems: 'center',
    cursor: 'pointer',
    transition: 'transform 0.3s ease, box-shadow 0.3s ease',
    border: `1px solid ${themes[theme].borderColor}`
  },
  actionIcon: {
    width: '40px',
    height: '40px',
    marginRight: '15px',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: '50%'
  },
  actionContent: {
    flex: 1
  },
  actionTitle: {
    fontSize: '18px',
    fontWeight: 'bold',
    marginBottom: '5px'
  },
  actionDescription: {
    fontSize: '14px',
    color: themes[theme].textColor,
    opacity: 0.8
  },
  reportsContainer: {
    display: 'grid',
    gridTemplateColumns: 'repeat(auto-fill, minmax(300px, 1fr))',
    gap: '20px',
    marginBottom: '30px'
  },
  reportCard: {
    backgroundColor: themes[theme].cardBackground,
    borderRadius: '8px',
    padding: '20px',
    boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1)',
    border: `1px solid ${themes[theme].borderColor}`
  },
  reportHeader: {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: '12px'
  },
  reportTitle: {
    fontSize: '18px',
    fontWeight: 'bold',
    margin: 0
  },
  reportCount: {
    backgroundColor: '#f0f0f0',
    color: '#333',
    borderRadius: '50%',
    width: '30px',
    height: '30px',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    fontWeight: 'bold'
  },
  reportItem: {
    padding: '10px 0',
    borderBottom: `1px solid ${themes[theme].borderColor}`,
    display: 'flex',
    justifyContent: 'space-between'
  },
  reportItemTitle: {
    fontSize: '14px'
  },
  reportItemDate: {
    fontSize: '12px',
    color: themes[theme].textColor,
    opacity: 0.7
  },
  recentReportsContainer: {
    backgroundColor: themes[theme].cardBackground,
    borderRadius: '8px',
    padding: '16px',
    boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1)',
    marginBottom: '30px',
    border: `1px solid ${themes[theme].borderColor}`,
    overflow: 'hidden',
    width: '100%',
    boxSizing: 'border-box' as 'border-box'
  },
  recentReportsGrid: {
    display: 'grid',
    gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))',
    gap: '12px',
    marginTop: '16px',
    maxWidth: '100%',
    overflow: 'hidden'
  },
  recentReportCard: {
    backgroundColor: themes[theme].cardBackground,
    borderRadius: '8px',
    padding: '12px',
    boxShadow: '0 2px 6px rgba(0, 0, 0, 0.08)',
    border: `1px solid ${themes[theme].borderColor}`,
    transition: 'all 0.3s ease',
    cursor: 'pointer',
    position: 'relative' as 'relative',
    maxWidth: '100%',
    minHeight: '140px',
    display: 'flex',
    flexDirection: 'column' as 'column'
  },
  recentReportCardHover: {
    transform: 'translateY(-2px)',
    boxShadow: '0 4px 12px rgba(0, 0, 0, 0.15)'
  },
  recentReportHeader: {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: '8px'
  },
  recentReportId: {
    fontSize: '11px',
    fontWeight: 'bold',
    color: '#3b82f6',
    backgroundColor: '#3b82f620',
    padding: '3px 6px',
    borderRadius: '4px',
    minWidth: '35px',
    textAlign: 'center' as 'center'
  },
  recentReportTitle: {
    fontSize: '14px',
    fontWeight: 'bold',
    color: themes[theme].textColor,
    marginBottom: '6px',
    lineHeight: '1.3',
    flex: 1
  },
  recentReportMeta: {
    display: 'flex',
    flexDirection: 'column' as 'column',
    gap: '4px',
    marginBottom: '8px',
    flex: 1
  },
  recentReportMetaRow: {
    display: 'flex',
    alignItems: 'center',
    gap: '6px',
    fontSize: '12px',
    color: themes[theme].textColor,
    opacity: 0.8
  },
  recentReportIcon: {
    width: '12px',
    height: '12px',
    opacity: 0.6
  },
  recentReportFooter: {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 'auto',
    paddingTop: '8px',
    borderTop: `1px solid ${themes[theme].borderColor}`
  },
  recentReportStatus: {
    padding: '4px 8px',
    borderRadius: '12px',
    fontSize: '11px',
    fontWeight: 'bold',
    textAlign: 'center' as 'center'
  },
  recentReportAction: {
    padding: '4px 8px',
    borderRadius: '4px',
    fontSize: '11px',
    fontWeight: 'bold',
    backgroundColor: '#3b82f6',
    color: 'white',
    border: 'none',
    cursor: 'pointer',
    transition: 'all 0.2s ease'
  },
  table: {
    width: '100%',
    borderCollapse: 'collapse' as 'collapse',
    marginTop: '15px'
  },
  tableHeader: {
    textAlign: 'left' as 'left',
    padding: '12px 15px',
    borderBottom: `1px solid ${themes[theme].borderColor}`,
    fontSize: '14px',
    fontWeight: 'bold'
  },
  tableCell: {
    padding: '12px 15px',
    borderBottom: `1px solid ${themes[theme].borderColor}`,
    fontSize: '14px'
  },
  statusBadge: {
    padding: '5px 10px',
    borderRadius: '4px',
    fontSize: '12px',
    fontWeight: 'bold',
    display: 'inline-block'
  },
  welcomeBanner: {
    backgroundColor: '#3b82f6',
    color: 'white',
    borderRadius: '8px',
    padding: '20px',
    marginBottom: '30px',
    display: 'flex',
    flexDirection: 'column' as 'column',
    alignItems: 'flex-start'
  },
  welcomeTitle: {
    fontSize: '24px',
    fontWeight: 'bold',
    marginBottom: '10px'
  },
  welcomeText: {
    fontSize: '16px',
    opacity: 0.9
  },
  footer: {
    textAlign: 'center' as 'center',
    marginTop: '10px',
    fontWeight: 'bold'
  }
  };
};

// Dados simulados para o dashboard
const mockData = {
  stats: [
    { title: 'Total de Relatórios', value: 248 },
    { title: 'Relatórios Pendentes', value: 35 },
    { title: 'Equipamentos Cadastrados', value: 156 },
    { title: 'Usuários Ativos', value: 12 }
  ],
  recentReports: [
    { id: 1, title: 'Inspeção Motor #123', date: '15/06/2023', local: 'Setor A', status: 'Concluído' },
    { id: 2, title: 'Manutenção Preventiva', date: '12/06/2023', local: 'Setor B', status: 'Pendente' },
    { id: 3, title: 'Análise de Vibração', date: '10/06/2023', local: 'Setor C', status: 'Concluído' },
    { id: 4, title: 'Troca de Componentes', date: '08/06/2023', local: 'Setor A', status: 'Concluído' },
    { id: 5, title: 'Calibração de Sensores', date: '05/06/2023', local: 'Setor D', status: 'Pendente' }
  ],
  openReports: [
    { title: 'Inspeção Emergencial', date: '18/06/2023' },
    { title: 'Verificação de Temperatura', date: '17/06/2023' },
    { title: 'Análise de Ruído', date: '16/06/2023' },
    { title: 'Teste de Pressão', date: '15/06/2023' }
  ],
  inProgressReports: [
    { title: 'Manutenção Corretiva', date: '18/06/2023' },
    { title: 'Substituição de Peças', date: '17/06/2023' },
    { title: 'Ajuste de Parâmetros', date: '16/06/2023' },
    { title: 'Limpeza de Componentes', date: '15/06/2023' }
  ]
};

const DashboardPage: React.FC = () => {
  const navigate = useNavigate();
  
  // Função para obter o tema inicial do localStorage ou usar 'light' como padrão
  const getInitialTheme = (): 'dark' | 'light' => {
    try {
      const savedTheme = localStorage.getItem('dashboard-theme');
      if (savedTheme === 'dark' || savedTheme === 'light') {
        return savedTheme;
      }
    } catch (error) {
      console.warn('Erro ao ler tema do localStorage:', error);
    }
    return 'light';
  };

  const [theme, setTheme] = useState<'dark' | 'light'>(getInitialTheme);
  const [sidebarOpen, setSidebarOpen] = useState(true);
  const styles = getStyles(theme);

  // useEffect para aplicar o tema na inicialização
  useEffect(() => {
    // Aplicar o tema ao body para garantir que toda a página use o tema correto
    document.body.style.backgroundColor = themes[theme].backgroundColor;
    document.body.style.color = themes[theme].textColor;
    
    // Adicionar classe de tema ao html para compatibilidade com outros componentes
    document.documentElement.setAttribute('data-theme', theme);
    
    return () => {
      // Cleanup: remover estilos quando o componente for desmontado
      document.body.style.backgroundColor = '';
      document.body.style.color = '';
      document.documentElement.removeAttribute('data-theme');
    };
  }, [theme]);

  // Função para alternar o tema e salvar no localStorage
  const toggleTheme = () => {
    setTheme(prevTheme => {
      const newTheme = prevTheme === 'light' ? 'dark' : 'light';
      try {
        localStorage.setItem('dashboard-theme', newTheme);
      } catch (error) {
        console.warn('Erro ao salvar tema no localStorage:', error);
      }
      return newTheme;
    });
  };

  // Função para alternar a barra lateral
  const toggleSidebar = () => {
    setSidebarOpen(prev => !prev);
  };

  // Função para fazer logout
  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  return (
    <div style={styles.mainContainer}>
      <TopBar 
        theme={theme}
        toggleTheme={toggleTheme}
        toggleSidebar={toggleSidebar}
        onLogout={handleLogout}
        isSidebarOpen={sidebarOpen}
      />
      <Sidebar isOpen={sidebarOpen} toggleSidebar={toggleSidebar} />
      
      <div style={{
        ...styles.content,
        ...(sidebarOpen ? {} : styles.contentCollapsed)
      }}>
        <div style={styles.container}>

          <div style={styles.welcomeBanner}>
            <h2 style={styles.welcomeTitle}>Bem-vindo ao Sistema de Relatórios! 👋</h2>
            <p style={styles.welcomeText}>Gerencie seus relatórios de forma eficiente e organizada</p>
          </div>

          <div style={styles.statsContainer}>
            {mockData.stats.map((stat, index) => (
              <div key={index} style={styles.statCard}>
                <div style={{
                  ...styles.statIcon,
                  backgroundColor: 
                    index === 0 ? '#3b82f6' : 
                    index === 1 ? '#10b981' : 
                    index === 2 ? '#f97316' : 
                    '#8b5cf6'
                }}>
                  {index === 0 ? '📄' : 
                   index === 1 ? '⏳' : 
                   index === 2 ? '🔧' : 
                   '👥'}
                </div>
                <div style={styles.statValue}>{stat.value}</div>
                <div style={styles.statTitle}>{stat.title}</div>
              </div>
            ))}
          </div>

          <div style={styles.actionsContainer}>
            <div 
              style={{
                ...styles.actionCard,
                backgroundColor: '#3b82f6',
                color: 'white'
              }}
              onClick={() => navigate('/create-report')}
            >
              <div style={{
                ...styles.actionIcon,
                backgroundColor: 'rgba(255, 255, 255, 0.2)'
              }}>
                +
              </div>
              <div style={styles.actionContent}>
                <div style={styles.actionTitle}>Novo Relatório</div>
                <div style={styles.actionDescription}>Criar um novo relatório</div>
              </div>
            </div>

            <div style={{
              ...styles.actionCard,
              backgroundColor: '#10b981',
              color: 'white'
            }}>
              <div style={{
                ...styles.actionIcon,
                backgroundColor: 'rgba(255, 255, 255, 0.2)'
              }}>
                📋
              </div>
              <div style={styles.actionContent}>
                <div style={styles.actionTitle}>Ver Relatórios</div>
                <div style={styles.actionDescription}>Gerenciar todos os relatórios</div>
              </div>
            </div>
          </div>

          <div style={styles.reportsContainer}>
            <div style={styles.reportCard}>
              <div style={styles.reportHeader}>
                <h3 style={styles.reportTitle}>Relatórios Abertos</h3>
                <div style={{
                  ...styles.reportCount,
                  backgroundColor: '#8b5cf6',
                  color: 'white'
                }}>
                  4
                </div>
              </div>
              {mockData.openReports.map((report, index) => (
                <div key={index} style={styles.reportItem}>
                  <div style={styles.reportItemTitle}>{report.title}</div>
                  <div style={styles.reportItemDate}>{report.date}</div>
                </div>
              ))}
            </div>

            <div style={styles.reportCard}>
              <div style={styles.reportHeader}>
                <h3 style={styles.reportTitle}>Em Andamento</h3>
                <div style={{
                  ...styles.reportCount,
                  backgroundColor: '#f97316',
                  color: 'white'
                }}>
                  16
                </div>
              </div>
              {mockData.inProgressReports.map((report, index) => (
                <div key={index} style={styles.reportItem}>
                  <div style={styles.reportItemTitle}>{report.title}</div>
                  <div style={styles.reportItemDate}>{report.date}</div>
                </div>
              ))}
            </div>
          </div>

          <div style={styles.recentReportsContainer}>
            <div style={styles.reportHeader}>
              <h3 style={styles.reportTitle}>Relatórios Recentes</h3>
              <div style={{
                ...styles.reportCount,
                backgroundColor: '#3b82f6',
                color: 'white'
              }}>
                {mockData.recentReports.length}
              </div>
            </div>
            
            <div style={styles.recentReportsGrid}>
              {mockData.recentReports.map((report, index) => (
                <div 
                  key={report.id} 
                  style={styles.recentReportCard}
                  onMouseEnter={(e) => {
                    Object.assign(e.currentTarget.style, styles.recentReportCardHover);
                  }}
                  onMouseLeave={(e) => {
                    Object.assign(e.currentTarget.style, styles.recentReportCard);
                  }}
                >
                  <div style={styles.recentReportHeader}>
                    <div style={styles.recentReportId}>#{report.id}</div>
                  </div>
                  
                  <div style={styles.recentReportTitle}>
                    {report.title}
                  </div>
                  
                  <div style={styles.recentReportMeta}>
                    <div style={styles.recentReportMetaRow}>
                      <span style={styles.recentReportIcon}>📅</span>
                      <span>{report.date}</span>
                    </div>
                    <div style={styles.recentReportMetaRow}>
                      <span style={styles.recentReportIcon}>📍</span>
                      <span>{report.local}</span>
                    </div>
                  </div>
                  
                  <div style={styles.recentReportFooter}>
                    <div style={{
                      ...styles.recentReportStatus,
                      backgroundColor: report.status === 'Concluído' ? '#10b981' : '#f97316',
                      color: 'white'
                    }}>
                      {report.status}
                    </div>
                    <button 
                      style={styles.recentReportAction}
                      onMouseEnter={(e) => {
                        e.currentTarget.style.backgroundColor = '#3b82f6dd';
                      }}
                      onMouseLeave={(e) => {
                        e.currentTarget.style.backgroundColor = '#3b82f6';
                      }}
                    >
                      Ver Detalhes
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>

          <div style={styles.footer}>
            Sistema de Relatórios © 2023
          </div>
        </div>
      </div>
    </div>
  );
};

export default DashboardPage;