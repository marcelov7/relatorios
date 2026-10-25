import React, { useState, useEffect, useRef } from 'react';
import Sidebar from '../components/Sidebar';
import TopBar from '../components/TopBar';

interface Equipment {
  id: number;
  tag: string;
  nome: string;
  setor: string;
  status: 'Operacional' | 'Manutenção' | 'Inativo';
}

interface ImagePreview {
  url: string;
  name: string;
  file: File;
}

interface FormData {
  titulo: string;
  activity: string;
  nome_responsavel: string;
  cargo_responsavel: string;
  date_created: string;
  time_created: string;
  equipment_ids: number[];
  progresso: number;
  status: string;
  detalhes: string;
  images: File[];
}

interface FormErrors {
  [key: string]: string;
}

// Definição de temas padrão
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

// Estilos inline para manter consistência
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
      marginTop: '60px',
      transition: 'margin-left 0.3s ease',
      overflowY: 'auto' as 'auto',
      height: 'calc(100vh - 60px)',
      backgroundColor: themes[theme].backgroundColor
    },
    contentCollapsed: {
      marginLeft: '0px'
    },
    container: {
      padding: '20px',
      maxWidth: '1200px',
      margin: '0 auto'
    }
  };
};

const CreateReportPage: React.FC = () => {
  // Estados do formulário
  const [formData, setFormData] = useState<FormData>({
    titulo: '',
    activity: '',
    nome_responsavel: 'João Silva', // Simulando usuário logado
    cargo_responsavel: 'Técnico de Manutenção', // Simulando cargo do usuário
    date_created: new Date().toISOString().split('T')[0],
    time_created: new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', hour12: false }),
    equipment_ids: [],
    progresso: 0,
    status: 'Aberta',
    detalhes: '',
    images: []
  });

  const [errors, setErrors] = useState<FormErrors>({});
  const [isSubmitting, setIsSubmitting] = useState(false);
  
  // Estados para equipamentos
  const [equipmentSearch, setEquipmentSearch] = useState('');
  const [equipmentList, setEquipmentList] = useState<Equipment[]>([]);
  const [selectedEquipments, setSelectedEquipments] = useState<Equipment[]>([]);
  const [loadingEquipments, setLoadingEquipments] = useState(false);
  
  // Estados para imagens
  const [imagePreviews, setImagePreviews] = useState<ImagePreview[]>([]);
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Estados para layout e tema
  const [sidebarOpen, setSidebarOpen] = useState(true);
  const [theme, setTheme] = useState<'light' | 'dark'>(() => {
    try {
      const savedTheme = localStorage.getItem('dashboard-theme');
      if (savedTheme === 'dark' || savedTheme === 'light') {
        return savedTheme;
      }
    } catch (error) {
      console.warn('Erro ao ler tema do localStorage:', error);
    }
    return 'light';
  });

  // Opções de atividade
  const activityOptions = [
    'Manutenção Preventiva',
    'Manutenção Corretiva',
    'Inspeção',
    'Calibração',
    'Limpeza',
    'Treinamento',
    'Auditoria',
    'Análise',
    'Instalação',
    'Outros'
  ];

  // Dados simulados de equipamentos expandidos
  const mockEquipments: Equipment[] = [
    { id: 1, tag: 'EQ001', nome: 'Compressor Principal', setor: 'Produção', status: 'Operacional' },
    { id: 2, tag: 'EQ002', nome: 'Bomba Centrífuga', setor: 'Utilidades', status: 'Manutenção' },
    { id: 3, tag: 'EQ003', nome: 'Motor Elétrico 1', setor: 'Produção', status: 'Operacional' },
    { id: 4, tag: 'EQ004', nome: 'Válvula de Controle', setor: 'Processo', status: 'Operacional' },
    { id: 5, tag: 'EQ005', nome: 'Sensor de Temperatura', setor: 'Instrumentação', status: 'Inativo' },
    { id: 6, tag: 'EQ006', nome: 'Bomba Centrífuga B', setor: 'Utilidades', status: 'Operacional' },
    { id: 7, tag: 'EQ007', nome: 'Ventilador Industrial', setor: 'Produção', status: 'Operacional' },
    { id: 8, tag: 'EQ008', nome: 'Transformador 1', setor: 'Elétrica', status: 'Operacional' },
    { id: 9, tag: 'EQ009', nome: 'Caldeira Principal', setor: 'Utilidades', status: 'Manutenção' },
    { id: 10, tag: 'EQ010', nome: 'Esteira Transportadora', setor: 'Produção', status: 'Operacional' },
    { id: 11, tag: 'EQ011', nome: 'Sensor de Pressão', setor: 'Instrumentação', status: 'Operacional' },
    { id: 12, tag: 'EQ012', nome: 'Motor Elétrico 2', setor: 'Produção', status: 'Inativo' },
    { id: 13, tag: 'EQ013', nome: 'Válvula Pneumática', setor: 'Processo', status: 'Operacional' },
    { id: 14, tag: 'EQ014', nome: 'Painel de Controle', setor: 'Elétrica', status: 'Operacional' },
    { id: 15, tag: 'EQ015', nome: 'Torre de Resfriamento', setor: 'Utilidades', status: 'Operacional' }
  ];

  // Função para buscar equipamentos com funcionalidades avançadas
  const searchEquipments = async (searchTerm: string) => {
    setLoadingEquipments(true);
    
    // Simulando busca com delay realista
    setTimeout(() => {
      if (!searchTerm.trim()) {
        setEquipmentList(mockEquipments);
        setLoadingEquipments(false);
        return;
      }

      const filtered = mockEquipments.filter(eq => {
        const searchLower = searchTerm.toLowerCase();
        return (
          eq.tag.toLowerCase().includes(searchLower) ||
          eq.nome.toLowerCase().includes(searchLower) ||
          eq.setor.toLowerCase().includes(searchLower) ||
          eq.status.toLowerCase().includes(searchLower)
        );
      });

      // Ordenar resultados por relevância
      const sorted = filtered.sort((a, b) => {
        const aScore = getRelevanceScore(a, searchTerm);
        const bScore = getRelevanceScore(b, searchTerm);
        return bScore - aScore;
      });

      setEquipmentList(sorted);
      setLoadingEquipments(false);
    }, Math.random() * 400 + 200); // Delay variável entre 200-600ms
  };

  // Função para calcular pontuação de relevância
  const getRelevanceScore = (equipment: Equipment, searchTerm: string): number => {
    const searchLower = searchTerm.toLowerCase();
    let score = 0;

    // TAG tem maior peso
    if (equipment.tag.toLowerCase().startsWith(searchLower)) score += 10;
    else if (equipment.tag.toLowerCase().includes(searchLower)) score += 5;

    // Nome tem peso médio
    if (equipment.nome.toLowerCase().startsWith(searchLower)) score += 8;
    else if (equipment.nome.toLowerCase().includes(searchLower)) score += 3;

    // Setor tem menor peso
    if (equipment.setor.toLowerCase().includes(searchLower)) score += 2;

    // Status operacional tem prioridade
    if (equipment.status === 'Operacional') score += 1;

    return score;
  };

  // Efeito para buscar equipamentos quando o termo de busca muda
  useEffect(() => {
    if (equipmentSearch.trim()) {
      searchEquipments(equipmentSearch);
    } else {
      setEquipmentList(mockEquipments);
    }
  }, [equipmentSearch]);

  // Carregar equipamentos inicialmente
  useEffect(() => {
    setEquipmentList(mockEquipments);
  }, []);

  // Atualizar status baseado no progresso
  useEffect(() => {
    const progresso = formData.progresso;
    let newStatus = '';
    
    if (progresso === 0) {
      newStatus = 'Aberta';
    } else if (progresso >= 1 && progresso <= 99) {
      newStatus = 'Em Andamento';
    } else if (progresso === 100) {
      newStatus = 'Concluída';
    }
    
    if (newStatus !== formData.status) {
      setFormData(prev => ({ ...prev, status: newStatus }));
    }
  }, [formData.progresso]);

  // Função para alternar a sidebar
  const toggleSidebar = () => {
    setSidebarOpen(prev => !prev);
  };

  // Função para lidar com mudanças nos campos do formulário
  const handleInputChange = (field: keyof FormData, value: any) => {
    setFormData(prev => ({ ...prev, [field]: value }));
    
    // Limpar erro do campo quando o usuário começar a digitar
    if (errors[field]) {
      setErrors(prev => ({ ...prev, [field]: '' }));
    }
  };

  // Função para lidar com seleção de equipamentos
  const handleEquipmentToggle = (equipment: Equipment) => {
    const isSelected = formData.equipment_ids.includes(equipment.id);
    
    if (isSelected) {
      // Remover equipamento
      setFormData(prev => ({
        ...prev,
        equipment_ids: prev.equipment_ids.filter(id => id !== equipment.id)
      }));
      setSelectedEquipments(prev => prev.filter(eq => eq.id !== equipment.id));
    } else {
      // Adicionar equipamento
      setFormData(prev => ({
        ...prev,
        equipment_ids: [...prev.equipment_ids, equipment.id]
      }));
      setSelectedEquipments(prev => [...prev, equipment]);
    }
  };

  // Função para remover equipamento selecionado
  const removeSelectedEquipment = (equipmentId: number) => {
    setFormData(prev => ({
      ...prev,
      equipment_ids: prev.equipment_ids.filter(id => id !== equipmentId)
    }));
    setSelectedEquipments(prev => prev.filter(eq => eq.id !== equipmentId));
  };

  // Tipos de arquivo de imagem aceitos
  const acceptedImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
  const maxFileSize = 5 * 1024 * 1024; // 5MB por arquivo
  const maxTotalFiles = 5;

  // Função para validar arquivo de imagem
  const validateImageFile = (file: File): string | null => {
    if (!acceptedImageTypes.includes(file.type)) {
      return `Tipo de arquivo não suportado. Use: ${acceptedImageTypes.map(type => type.split('/')[1].toUpperCase()).join(', ')}`;
    }
    
    if (file.size > maxFileSize) {
      return `Arquivo muito grande. Máximo ${Math.round(maxFileSize / (1024 * 1024))}MB por imagem`;
    }
    
    if (imagePreviews.length >= maxTotalFiles) {
      return `Máximo de ${maxTotalFiles} imagens permitidas`;
    }
    
    // Verificar se já existe
    const exists = imagePreviews.some(preview => 
      preview.name === file.name && preview.file.size === file.size
    );
    
    if (exists) {
      return 'Esta imagem já foi adicionada';
    }
    
    return null;
  };

  // Função para processar arquivos de imagem
  const processImageFiles = (files: File[]) => {
    const validFiles: File[] = [];
    const errors: string[] = [];

    files.forEach(file => {
      const error = validateImageFile(file);
      if (error) {
        errors.push(`${file.name}: ${error}`);
      } else {
        validFiles.push(file);
      }
    });

    // Mostrar erros se houver
    if (errors.length > 0) {
      alert(`Erros encontrados:\n${errors.join('\n')}`);
    }

    // Processar arquivos válidos
    validFiles.forEach(file => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const newPreview: ImagePreview = {
          url: e.target?.result as string,
          name: file.name,
          file: file
        };
        
        setImagePreviews(prev => [...prev, newPreview]);
        setFormData(prev => ({
          ...prev,
          images: [...prev.images, file]
        }));
      };
      reader.readAsDataURL(file);
    });
  };

  // Função para lidar com upload de imagens
  const handleFileUpload = (event: React.ChangeEvent<HTMLInputElement>) => {
    const files = Array.from(event.target.files || []);
    processImageFiles(files);
    
    // Limpar input
    if (event.target) {
      event.target.value = '';
    }
  };

  // Função para lidar com drag and drop
  const handleDragOver = (event: React.DragEvent<HTMLDivElement>) => {
    event.preventDefault();
    event.stopPropagation();
  };

  const handleDragEnter = (event: React.DragEvent<HTMLDivElement>) => {
    event.preventDefault();
    event.stopPropagation();
  };

  const handleDragLeave = (event: React.DragEvent<HTMLDivElement>) => {
    event.preventDefault();
    event.stopPropagation();
  };

  const handleDrop = (event: React.DragEvent<HTMLDivElement>) => {
    event.preventDefault();
    event.stopPropagation();
    
    const files = Array.from(event.dataTransfer.files);
    const imageFiles = files.filter(file => file.type.startsWith('image/'));
    
    if (imageFiles.length !== files.length) {
      alert('Apenas arquivos de imagem são aceitos');
    }
    
    if (imageFiles.length > 0) {
      processImageFiles(imageFiles);
    }
  };

  // Função para remover imagem
  const removeImage = (index: number) => {
    setImagePreviews(prev => prev.filter((_, i) => i !== index));
    setFormData(prev => ({
      ...prev,
      images: prev.images.filter((_, i) => i !== index)
    }));
    
    // Limpar erro de imagens se houver
    if (errors.images) {
      setErrors(prev => ({ ...prev, images: '' }));
    }
  };

  // Função para formatar tamanho do arquivo
  const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  };

  // Função para validar formulário
  const validateForm = (): boolean => {
    const newErrors: FormErrors = {};
    
    // Validação do título
    if (!formData.titulo.trim()) {
      newErrors.titulo = 'Título é obrigatório';
    } else if (formData.titulo.trim().length < 3) {
      newErrors.titulo = 'Título deve ter pelo menos 3 caracteres';
    } else if (formData.titulo.trim().length > 100) {
      newErrors.titulo = 'Título deve ter no máximo 100 caracteres';
    }
    
    // Validação da atividade
     if (!formData.activity.trim()) {
       newErrors.activity = 'Atividade é obrigatória';
     } else if (formData.activity.trim().length < 5) {
       newErrors.activity = 'Atividade deve ter pelo menos 5 caracteres';
     } else if (formData.activity.trim().length > 200) {
       newErrors.activity = 'Atividade deve ter no máximo 200 caracteres';
     }

     // Validação do nome do responsável
     if (!formData.nome_responsavel.trim()) {
       newErrors.nome_responsavel = 'Nome do responsável é obrigatório';
     } else if (formData.nome_responsavel.trim().length < 2) {
       newErrors.nome_responsavel = 'Nome deve ter pelo menos 2 caracteres';
     } else if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(formData.nome_responsavel.trim())) {
       newErrors.nome_responsavel = 'Nome deve conter apenas letras e espaços';
     }

     // Validação do cargo
     if (!formData.cargo_responsavel.trim()) {
       newErrors.cargo_responsavel = 'Cargo do responsável é obrigatório';
     } else if (formData.cargo_responsavel.trim().length < 2) {
       newErrors.cargo_responsavel = 'Cargo deve ter pelo menos 2 caracteres';
     }

     // Validação da data
     if (!formData.date_created) {
       newErrors.date_created = 'Data é obrigatória';
     } else {
       const selectedDate = new Date(formData.date_created);
       const today = new Date();
       today.setHours(0, 0, 0, 0);
       
       if (selectedDate > today) {
         newErrors.date_created = 'Data não pode ser futura';
       }
       
       const oneYearAgo = new Date();
       oneYearAgo.setFullYear(oneYearAgo.getFullYear() - 1);
       
       if (selectedDate < oneYearAgo) {
         newErrors.date_created = 'Data não pode ser anterior a um ano';
       }
     }

     // Validação da hora
     if (!formData.time_created.trim()) {
       newErrors.time_created = 'Hora é obrigatória';
     } else {
       const timeRegex = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
       if (!timeRegex.test(formData.time_created)) {
         newErrors.time_created = 'Formato de hora inválido (HH:MM)';
       }
     }

     // Validação dos equipamentos
     if (formData.equipment_ids.length === 0) {
       newErrors.equipment_ids = 'É obrigatório selecionar pelo menos um equipamento';
     } else if (formData.equipment_ids.length > 10) {
       newErrors.equipment_ids = 'Máximo de 10 equipamentos permitidos';
     }

     // Validação do progresso
     if (formData.progresso < 0 || formData.progresso > 100) {
       newErrors.progresso = 'Progresso deve estar entre 0 e 100%';
     }

     // Validação dos detalhes
     if (!formData.detalhes.trim()) {
       newErrors.detalhes = 'Detalhes são obrigatórios';
     } else if (formData.detalhes.trim().length < 10) {
       newErrors.detalhes = 'Detalhes devem ter pelo menos 10 caracteres';
     } else if (formData.detalhes.trim().length > 2000) {
       newErrors.detalhes = 'Detalhes devem ter no máximo 2000 caracteres';
     }

     // Validação das imagens
     if (formData.images.length > 5) {
       newErrors.images = 'Máximo de 5 imagens permitidas';
     }

     // Verificar tamanho total das imagens
     const totalSize = formData.images.reduce((sum, img) => sum + (img.size || 0), 0);
     const maxTotalSize = 10 * 1024 * 1024; // 10MB
     if (totalSize > maxTotalSize) {
       newErrors.images = 'Tamanho total das imagens não pode exceder 10MB';
     }
    
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  // Função para submeter formulário
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validateForm()) {
      return;
    }
    
    setIsSubmitting(true);
    
    try {
      // Simular envio do formulário
      console.log('Dados do formulário:', formData);
      console.log('Imagens:', imagePreviews);
      
      // Simular delay de envio
      await new Promise(resolve => setTimeout(resolve, 2000));
      
      alert('Relatório criado com sucesso!');
      
      // Reset do formulário
      resetForm();
      
    } catch (error) {
      console.error('Erro ao criar relatório:', error);
      alert('Erro ao criar relatório. Tente novamente.');
    } finally {
      setIsSubmitting(false);
    }
  };

  // Função para resetar formulário
  const resetForm = () => {
    setFormData({
      titulo: '',
      activity: '',
      nome_responsavel: 'João Silva',
      cargo_responsavel: 'Técnico de Manutenção',
      date_created: new Date().toISOString().split('T')[0],
      time_created: new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', hour12: false }),
      equipment_ids: [],
      progresso: 0,
      status: 'Aberta',
      detalhes: '',
      images: []
    });
    setSelectedEquipments([]);
    setImagePreviews([]);
    setEquipmentSearch('');
    setErrors({});
  };

  // Função para obter classe do status
  const getStatusClass = (status: string) => {
    const classes = {
      'Aberta': 'border-red-300 bg-red-50 text-red-900',
      'Em Andamento': 'border-yellow-300 bg-yellow-50 text-yellow-900',
      'Concluída': 'border-green-300 bg-green-50 text-green-900',
      'Cancelada': 'border-gray-300 bg-gray-50 text-gray-900'
    };
    return classes[status as keyof typeof classes] || 'border-gray-300 bg-gray-50 text-gray-900';
  };

  // Função para obter classe da barra de progresso
  const getProgressBarClass = (progresso: number) => {
    if (progresso >= 100) return 'bg-green-500';
    if (progresso >= 75) return 'bg-blue-500';
    if (progresso >= 50) return 'bg-yellow-500';
    if (progresso >= 25) return 'bg-orange-500';
    return 'bg-red-500';
  };

  // Função para obter classe do status do equipamento
  const getEquipmentStatusClass = (status: string) => {
    const classes = {
      'Operacional': 'bg-green-100 text-green-800',
      'Manutenção': 'bg-yellow-100 text-yellow-800',
      'Inativo': 'bg-gray-100 text-gray-800'
    };
    return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800';
  };

  const styles = getStyles(theme);

  return (
    <div style={styles.mainContainer}>
      {/* TopBar */}
      <TopBar
        theme={theme}
        toggleTheme={() => {
          const newTheme = theme === 'light' ? 'dark' : 'light';
          setTheme(newTheme);
          localStorage.setItem('dashboard-theme', newTheme);
        }}
        toggleSidebar={toggleSidebar}
        onLogout={() => window.history.back()}
        isSidebarOpen={sidebarOpen}
      />
      
      {/* Sidebar */}
      <Sidebar isOpen={sidebarOpen} toggleSidebar={toggleSidebar} />
      
      {/* Conteúdo Principal */}
      <div 
        style={{
          ...styles.content,
          ...(sidebarOpen ? {} : styles.contentCollapsed)
        }}
      >
        <div style={styles.container}>
          <div className="max-w-4xl mx-auto p-3 sm:p-6">
            <form onSubmit={handleSubmit}>
              <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Card 1: Informações Básicas */}
                <div className="lg:col-span-2">
                  <div 
                    style={{
                      backgroundColor: themes[theme].cardBackground,
                      borderColor: themes[theme].borderColor,
                      color: themes[theme].textColor
                    }}
                    className="rounded-lg border shadow-sm"
                  >
                    <div className="p-4 sm:p-6">
                      <h3 className={`text-lg font-semibold ${theme === 'dark' ? 'text-gray-200' : 'text-gray-800'} mb-4 border-b ${theme === 'dark' ? 'border-gray-700' : 'border-gray-200'} pb-2`}>
                        Informações Básicas
                      </h3>
                      <div className="space-y-4">
                        {/* Título */}
                        <div>
                          <label htmlFor="titulo" className={`block text-sm font-medium ${theme === 'dark' ? 'text-gray-300' : 'text-gray-700'} mb-1.5`}>
                            Título *
                          </label>
                          <input
                            id="titulo"
                            type="text"
                            value={formData.titulo}
                            onChange={(e) => handleInputChange('titulo', e.target.value)}
                            className={`w-full rounded-lg border ${errors.titulo ? 'border-red-500' : theme === 'dark' ? 'border-gray-600 bg-gray-700 text-gray-300' : 'border-gray-300'} px-3 py-2.5 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm`}
                            placeholder="Título do relatório"
                            required
                          />
                          {errors.titulo && (
                            <div className="text-red-500 text-xs mt-1">{errors.titulo}</div>
                          )}
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                          {/* Atividade */}
                          <div>
                            <label htmlFor="activity" className={`block text-sm font-medium ${theme === 'dark' ? 'text-gray-300' : 'text-gray-700'} mb-1.5`}>
                              Atividade *
                            </label>
                            <select
                              id="activity"
                              value={formData.activity}
                              onChange={(e) => handleInputChange('activity', e.target.value)}
                              className={`w-full rounded-lg border ${errors.activity ? 'border-red-500' : theme === 'dark' ? 'border-gray-600 bg-gray-700 text-gray-300' : 'border-gray-300'} px-3 py-2.5 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm`}
                              required
                            >
                              <option value="">Selecione uma atividade</option>
                              {activityOptions.map(option => (
                                <option key={option} value={option}>{option}</option>
                              ))}
                            </select>
                            {errors.activity && (
                              <div className="text-red-500 text-xs mt-1">{errors.activity}</div>
                            )}
                          </div>

                          {/* Nome do Responsável */}
                          <div>
                            <label htmlFor="nome_responsavel" className={`block text-sm font-medium ${theme === 'dark' ? 'text-gray-300' : 'text-gray-700'} mb-1.5`}>
                              Nome do Responsável *
                            </label>
                            <input
                              id="nome_responsavel"
                              type="text"
                              value={formData.nome_responsavel}
                              readOnly
                              className={`w-full rounded-lg border ${theme === 'dark' ? 'border-gray-600 bg-gray-600 text-gray-300' : 'border-gray-300 bg-gray-100 text-gray-700'} px-3 py-2.5 shadow-sm cursor-not-allowed text-sm`}
                            />
                            <p className={`text-xs ${theme === 'dark' ? 'text-gray-400' : 'text-gray-500'} mt-1`}>
                              Preenchido automaticamente com seu nome do perfil
                            </p>
                          </div>
                        </div>

                        {/* Cargo do Responsável */}
                        <div>
                          <label htmlFor="cargo_responsavel" className={`block text-sm font-medium ${theme === 'dark' ? 'text-gray-300' : 'text-gray-700'} mb-1.5`}>
                            Cargo do Responsável
                          </label>
                          <input
                            id="cargo_responsavel"
                            type="text"
                            value={formData.cargo_responsavel}
                            readOnly
                            className={`w-full rounded-lg border ${theme === 'dark' ? 'border-gray-600 bg-gray-600 text-gray-300' : 'border-gray-300 bg-gray-100 text-gray-700'} px-3 py-2.5 shadow-sm cursor-not-allowed text-sm`}
                          />
                          <p className={`text-xs ${theme === 'dark' ? 'text-gray-400' : 'text-gray-500'} mt-1`}>
                            Preenchido automaticamente com seu cargo do perfil
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {/* Card 2: Data e Hora */}
                <div>
                  <div 
                    style={{
                      backgroundColor: themes[theme].cardBackground,
                      borderColor: themes[theme].borderColor,
                      color: themes[theme].textColor
                    }}
                    className="rounded-lg border shadow-sm"
                  >
                    <div className="p-4 sm:p-6">
                      <h3 className={`text-lg font-semibold ${theme === 'dark' ? 'text-gray-200' : 'text-gray-800'} mb-4 border-b ${theme === 'dark' ? 'border-gray-700' : 'border-gray-200'} pb-2`}>
                        Data e Hora de Criação
                      </h3>
                      <div className="space-y-4">
                        <div>
                          <label htmlFor="date_created" className={`block text-sm font-medium ${theme === 'dark' ? 'text-gray-300' : 'text-gray-700'} mb-1.5`}>
                            Data *
                          </label>
                          <input
                            id="date_created"
                            type="date"
                            value={formData.date_created}
                            onChange={(e) => handleInputChange('date_created', e.target.value)}
                            className={`w-full rounded-lg border ${errors.date_created ? 'border-red-500' : theme === 'dark' ? 'border-gray-600 bg-gray-700 text-gray-300' : 'border-gray-300'} px-3 py-2.5 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm`}
                            required
                          />
                          {errors.date_created && (
                            <div className="text-red-500 text-xs mt-1">{errors.date_created}</div>
                          )}
                        </div>

                        <div>
                          <label htmlFor="time_created" className={`block text-sm font-medium ${theme === 'dark' ? 'text-gray-300' : 'text-gray-700'} mb-1.5`}>
                            Hora
                          </label>
                          <input
                            id="time_created"
                            type="time"
                            value={formData.time_created}
                            onChange={(e) => handleInputChange('time_created', e.target.value)}
                            className={`w-full rounded-lg border ${theme === 'dark' ? 'border-gray-600 bg-gray-700 text-gray-300' : 'border-gray-300'} px-3 py-2.5 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm`}
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {/* Card 3: Equipamentos */}
                <div>
                  <div 
                    style={{
                      backgroundColor: themes[theme].cardBackground,
                      borderColor: themes[theme].borderColor,
                      color: themes[theme].textColor
                    }}
                    className="rounded-lg border shadow-sm"
                  >
                    <div className="p-4 sm:p-6">
                      <h3 className={`text-lg font-semibold ${theme === 'dark' ? 'text-gray-200' : 'text-gray-800'} mb-4 border-b ${theme === 'dark' ? 'border-gray-700' : 'border-gray-200'} pb-2`}>
                        Equipamentos
                      </h3>
                      <div>
                        <label className={`block text-sm font-medium ${theme === 'dark' ? 'text-gray-300' : 'text-gray-700'} mb-1.5`}>
                          Equipamentos *
                        </label>
                      
                        {/* Busca de equipamentos */}
                        <div className="mb-3 relative">
                          <div className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                          </div>
                          <input
                            type="text"
                            value={equipmentSearch}
                            onChange={(e) => setEquipmentSearch(e.target.value)}
                            placeholder="Buscar por nome, TAG ou setor..."
                            className={`w-full pl-9 rounded-lg border ${theme === 'dark' ? 'border-gray-600 bg-gray-700 text-gray-300' : 'border-gray-300'} px-3 py-2.5 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm`}
                          />
                        </div>

                        {/* Lista de equipamentos */}
                        {loadingEquipments ? (
                          <div className={`text-xs ${theme === 'dark' ? 'text-gray-400' : 'text-gray-500'} py-2`}>
                            Carregando equipamentos...
                          </div>
                        ) : equipmentList.length === 0 ? (
                          <div className={`text-xs ${theme === 'dark' ? 'text-gray-400' : 'text-gray-500'} py-2`}>
                            Nenhum equipamento encontrado.
                          </div>
                        ) : (
                          <div className={`space-y-1.5 max-h-40 overflow-y-auto border ${theme === 'dark' ? 'border-gray-600 bg-gray-700' : 'border-gray-300 bg-gray-50'} rounded-lg p-2`}>
                            {equipmentList.map(equipment => (
                              <div
                                key={equipment.id}
                                className={`flex items-start space-x-2.5 p-2 ${theme === 'dark' ? 'hover:bg-gray-600' : 'hover:bg-gray-100'} rounded-md transition-colors duration-150`}
                              >
                                <input
                                  id={`equipment-${equipment.id}`}
                                  type="checkbox"
                                  checked={formData.equipment_ids.includes(equipment.id)}
                                  onChange={() => handleEquipmentToggle(equipment)}
                                  className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-0.5"
                                />
                                <label
                                  htmlFor={`equipment-${equipment.id}`}
                                  className="flex-1 cursor-pointer"
                                >
                                  <div className="space-y-1">
                                    <div className="flex items-center justify-between">
                                      <span className={`text-sm font-medium ${theme === 'dark' ? 'text-gray-100' : 'text-gray-900'}`}>
                                        {equipment.tag}
                                      </span>
                                      <span className={`text-xs px-1.5 py-0.5 rounded-full ${getEquipmentStatusClass(equipment.status)}`}>
                                        {equipment.status}
                                      </span>
                                    </div>
                                    <div className="flex items-center space-x-2">
                                      <span className={`text-xs ${theme === 'dark' ? 'text-gray-400' : 'text-gray-600'}`}>
                                        {equipment.nome}
                                      </span>
                                      <span className={`text-xs px-1.5 py-0.5 rounded-full ${theme === 'dark' ? 'bg-gray-800 text-gray-300' : 'bg-gray-100 text-gray-700'}`}>
                                        {equipment.setor}
                                      </span>
                                    </div>
                                  </div>
                                </label>
                              </div>
                            ))}
                          </div>
                        )}

                        {/* Equipamentos selecionados */}
                        {selectedEquipments.length > 0 && (
                          <div className="mt-3">
                            <label className={`block text-sm font-medium ${theme === 'dark' ? 'text-gray-300' : 'text-gray-700'} mb-1.5`}>
                              Equipamentos Selecionados:
                            </label>
                            <div className="flex flex-wrap gap-1.5">
                              {selectedEquipments.map(equipment => (
                                <span
                                  key={equipment.id}
                                  className={`inline-flex items-center px-2 py-1 rounded-lg ${theme === 'dark' ? 'bg-blue-900 text-blue-200' : 'bg-blue-100 text-blue-800'} text-xs`}
                                >
                                  <span className="font-medium mr-1">{equipment.tag}</span>
                                  <span className={`px-1.5 py-0.5 rounded ${theme === 'dark' ? 'bg-gray-700 text-gray-300' : 'bg-gray-200 text-gray-700'} text-xs`}>
                                    {equipment.setor}
                                  </span>
                                  <button
                                    type="button"
                                    onClick={() => removeSelectedEquipment(equipment.id)}
                                    className={`ml-1.5 ${theme === 'dark' ? 'text-red-300 hover:text-red-100' : 'text-red-500 hover:text-red-700'} focus:outline-none text-sm`}
                                  >
                                    ×
                                  </button>
                                </span>
                              ))}
                            </div>
                          </div>
                        )}

                        {errors.equipment_ids && (
                          <div className="text-red-500 text-xs mt-1">{errors.equipment_ids}</div>
                        )}
                      </div>
                    </div>
                  </div>
                </div>

                {/* Card 4: Progresso e Status */}
                <div>
                  <div 
                    style={{
                      backgroundColor: themes[theme].cardBackground,
                      borderColor: themes[theme].borderColor,
                      color: themes[theme].textColor
                    }}
                    className="rounded-lg border shadow-sm"
                  >
                    <div className="p-4 sm:p-6">
                      <h3 className={`text-lg font-semibold ${theme === 'dark' ? 'text-gray-200' : 'text-gray-800'} mb-4 border-b ${theme === 'dark' ? 'border-gray-700' : 'border-gray-200'} pb-2`}>
                        Progresso e Status
                      </h3>
                      <div className="space-y-4">
                        {/* Progresso */}
                        <div>
                          <label htmlFor="progresso" className={`block text-sm font-medium ${theme === 'dark' ? 'text-gray-300' : 'text-gray-700'} mb-1.5`}>
                            Progresso: {formData.progresso}%
                          </label>
                          <div className="space-y-2">
                            {/* Slider */}
                            <input
                              id="progresso"
                              type="range"
                              min="0"
                              max="100"
                              step="1"
                              value={formData.progresso}
                              onChange={(e) => handleInputChange('progresso', parseInt(e.target.value))}
                              className={`w-full h-2 ${theme === 'dark' ? 'bg-gray-600' : 'bg-gray-200'} rounded-lg appearance-none cursor-pointer`}
                            />
                            
                            {/* Barra de progresso visual */}
                            <div className={`w-full ${theme === 'dark' ? 'bg-gray-700' : 'bg-gray-200'} rounded-full h-2.5`}>
                              <div 
                                className={`h-full rounded-full transition-all duration-300 ease-out ${getProgressBarClass(formData.progresso)}`}
                                style={{ width: `${formData.progresso}%` }}
                              ></div>
                            </div>
                            
                            {/* Marcadores de progresso */}
                            <div className={`flex justify-between text-xs ${theme === 'dark' ? 'text-gray-400' : 'text-gray-500'}`}>
                              <span>0%</span>
                              <span>25%</span>
                              <span>50%</span>
                              <span>75%</span>
                              <span>100%</span>
                            </div>
                          </div>
                        </div>

                        {/* Status */}
                        <div>
                          <label htmlFor="status" className={`block text-sm font-medium ${theme === 'dark' ? 'text-gray-300' : 'text-gray-700'} mb-1.5`}>
                            Status
                          </label>
                          <div className="relative">
                            <input
                              id="status"
                              type="text"
                              value={formData.status}
                              readOnly
                              className={`w-full rounded-lg border px-3 py-2.5 shadow-sm text-sm ${getStatusClass(formData.status)}`}
                            />
                            <div className="absolute inset-y-0 right-0 flex items-center pr-3">
                              <div className={`w-2.5 h-2.5 rounded-full transition-colors duration-300 ${
                                formData.status === 'Aberta' ? 'bg-red-500' :
                                formData.status === 'Em Andamento' ? 'bg-yellow-500' :
                                formData.status === 'Concluída' ? 'bg-green-500' : 'bg-gray-500'
                              }`}></div>
                            </div>
                          </div>
                          <p className={`text-xs ${theme === 'dark' ? 'text-gray-400' : 'text-gray-500'} mt-1`}>
                            Status atualizado automaticamente baseado no progresso
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {/* Card 5: Detalhes */}
                <div className="lg:col-span-2">
                  <div 
                    style={{
                      backgroundColor: themes[theme].cardBackground,
                      borderColor: themes[theme].borderColor,
                      color: themes[theme].textColor
                    }}
                    className="rounded-lg border shadow-sm"
                  >
                    <div className="p-4 sm:p-6">
                      <h3 className={`text-lg font-semibold ${theme === 'dark' ? 'text-gray-200' : 'text-gray-800'} mb-4 border-b ${theme === 'dark' ? 'border-gray-700' : 'border-gray-200'} pb-2`}>
                        Detalhes
                      </h3>
                      <div>
                        <label htmlFor="detalhes" className={`block text-sm font-medium ${theme === 'dark' ? 'text-gray-300' : 'text-gray-700'} mb-1.5`}>
                          Detalhes *
                        </label>
                        <textarea
                          id="detalhes"
                          value={formData.detalhes}
                          onChange={(e) => handleInputChange('detalhes', e.target.value)}
                          rows={5}
                          className={`w-full rounded-lg border ${errors.detalhes ? 'border-red-500' : theme === 'dark' ? 'border-gray-600 bg-gray-700 text-gray-300' : 'border-gray-300'} px-3 py-2.5 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm`}
                          placeholder="Descreva os detalhes do relatório..."
                          required
                        />
                        {errors.detalhes && (
                          <div className="text-red-500 text-xs mt-1">{errors.detalhes}</div>
                        )}
                      </div>
                    </div>
                  </div>
                </div>

                {/* Card 6: Upload de Imagens */}
                <div className="lg:col-span-2">
                  <div 
                    style={{
                      backgroundColor: themes[theme].cardBackground,
                      borderColor: themes[theme].borderColor,
                      color: themes[theme].textColor
                    }}
                    className="rounded-lg border shadow-sm"
                  >
                    <div className="p-4 sm:p-6">
                      <h3 className={`text-lg font-semibold ${theme === 'dark' ? 'text-gray-200' : 'text-gray-800'} mb-4 border-b ${theme === 'dark' ? 'border-gray-700' : 'border-gray-200'} pb-2`}>
                        Imagens
                      </h3>
                      <div>
                        <label className={`block text-sm font-medium ${theme === 'dark' ? 'text-gray-300' : 'text-gray-700'} mb-1.5`}>
                          Imagens
                        </label>
                      
                        {/* Área de Upload com Drag and Drop */}
                        <div 
                          className={`border-2 border-dashed ${theme === 'dark' ? 'border-gray-600 hover:border-gray-500' : 'border-gray-300 hover:border-gray-400'} rounded-lg p-4 text-center transition-colors duration-200 cursor-pointer`}
                          onDragOver={handleDragOver}
                          onDragEnter={handleDragEnter}
                          onDragLeave={handleDragLeave}
                          onDrop={handleDrop}
                          onClick={() => fileInputRef.current?.click()}
                        >
                          <input
                            ref={fileInputRef}
                            type="file"
                            multiple
                            accept={acceptedImageTypes.join(',')}
                            className="hidden"
                            onChange={handleFileUpload}
                          />
                          <div className="space-y-1.5">
                            <svg className={`mx-auto h-8 w-8 ${theme === 'dark' ? 'text-gray-400' : 'text-gray-400'}`} stroke="currentColor" fill="none" viewBox="0 0 48 48">
                              <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                            <div className={`text-xs ${theme === 'dark' ? 'text-gray-400' : 'text-gray-600'}`}>
                              <span className={`font-medium ${theme === 'dark' ? 'text-blue-400 hover:text-blue-300' : 'text-blue-600 hover:text-blue-500'}`}>
                                Clique para adicionar imagens
                              </span>
                              {' '}ou arraste e solte aqui
                            </div>
                            <p className={`text-xs ${theme === 'dark' ? 'text-gray-400' : 'text-gray-500'}`}>
                              JPEG, PNG, GIF, WEBP até {Math.round(maxFileSize / (1024 * 1024))}MB cada (máx. {maxTotalFiles})
                            </p>
                            {imagePreviews.length > 0 && (
                              <p className={`text-xs ${theme === 'dark' ? 'text-green-400' : 'text-green-600'}`}>
                                {imagePreviews.length} de {maxTotalFiles} imagens adicionadas
                              </p>
                            )}
                          </div>
                        </div>
                        
                        {/* Mostrar erro de imagens */}
                        {errors.images && (
                          <p className="mt-1 text-xs text-red-500">{errors.images}</p>
                        )}

                        {/* Preview das Imagens */}
                        {imagePreviews.length > 0 && (
                          <div className="mt-3">
                            <h4 className={`text-sm font-medium ${theme === 'dark' ? 'text-gray-300' : 'text-gray-700'} mb-2`}>
                              Imagens Selecionadas ({imagePreviews.length})
                            </h4>
                            <div className="grid grid-cols-2 sm:grid-cols-3 gap-2">
                              {imagePreviews.map((preview, index) => (
                                <div key={index} className={`relative group border ${theme === 'dark' ? 'border-gray-600' : 'border-gray-300'} rounded-lg overflow-hidden`}>
                                  <img
                                    src={preview.url}
                                    alt={preview.name}
                                    className="w-full h-24 object-cover"
                                  />
                                  <div className="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center">
                                    <button
                                      type="button"
                                      onClick={() => removeImage(index)}
                                      className="opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 shadow-lg"
                                      title="Remover imagem"
                                    >
                                      <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                      </svg>
                                    </button>
                                  </div>
                                  <div className={`p-1.5 ${theme === 'dark' ? 'bg-gray-700' : 'bg-gray-50'}`}>
                                    <p className={`text-xs ${theme === 'dark' ? 'text-gray-300' : 'text-gray-700'} font-medium truncate`} title={preview.name}>
                                      {preview.name}
                                    </p>
                                    <p className={`text-xs ${theme === 'dark' ? 'text-gray-400' : 'text-gray-500'}`}>
                                      {formatFileSize(preview.file.size)}
                                    </p>
                                  </div>
                                </div>
                              ))}
                            </div>
                          </div>
                        )}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              {/* Botões de ação */}
              <div className="mt-6">
                <div 
                  style={{
                    backgroundColor: themes[theme].cardBackground,
                    borderColor: themes[theme].borderColor,
                    color: themes[theme].textColor
                  }}
                  className="rounded-lg border shadow-sm"
                >
                  <div className="p-4 sm:p-6">
                    <div className="flex flex-col sm:flex-row gap-3">
                      <button
                        type="submit"
                        disabled={isSubmitting}
                        className={`flex-1 inline-flex items-center justify-center px-4 py-3 ${theme === 'dark' ? 'bg-blue-500 hover:bg-blue-600' : 'bg-blue-600 hover:bg-blue-700'} border border-transparent rounded-lg font-medium text-sm text-white focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50`}
                      >
                        {isSubmitting && (
                          <svg className="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                        )}
                        {isSubmitting ? 'Criando...' : 'Criar Relatório'}
                      </button>
                      
                      <div className="flex gap-3 sm:gap-2">
                        <button
                          type="button"
                          onClick={resetForm}
                          className={`flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-3 ${theme === 'dark' ? 'bg-gray-600 text-gray-200 hover:bg-gray-500' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'} border border-transparent rounded-lg font-medium text-sm focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150`}
                        >
                          Limpar
                        </button>
                        
                        <button
                          type="button"
                          onClick={() => window.history.back()}
                          className={`flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-3 ${theme === 'dark' ? 'bg-gray-600 text-gray-200 hover:bg-gray-500' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'} border border-transparent rounded-lg font-medium text-sm focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150`}
                        >
                          Cancelar
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CreateReportPage;