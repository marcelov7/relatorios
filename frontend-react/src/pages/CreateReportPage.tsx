import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import TopBar from '../components/TopBar';
import Sidebar from '../components/Sidebar';
import { 
  FiInfo, 
  FiClock, 
  FiMapPin, 
  FiTool, 
  FiImage, 
  FiClipboard, 
  FiCheckCircle,
  FiChevronRight,
  FiChevronLeft,
  FiSave,
  FiX,
  FiPlus
} from 'react-icons/fi';
import DatePicker from 'react-datepicker';
import "react-datepicker/dist/react-datepicker.css";
import { registerLocale } from 'react-datepicker';
import { ptBR } from 'date-fns/locale/pt-BR';
import { format } from 'date-fns';
import axios from 'axios';

// Registra o locale pt-BR para o DatePicker
registerLocale('pt-BR', ptBR);

// Interface para Equipamento
interface Equipment {
  id: number;
  nome: string;
  tipo: string;
  status: string;
  setor: string;
}

// Interface para ImagePreview
interface ImagePreview {
  file: File;
  preview: string;
}

// Interface para FormData
interface FormData {
  titulo: string;
  atividade: string;
  responsavel: string;
  cargo: string;
  dataInicio: Date | null;
  dataFim: Date | null;
  local: string;
  equipamento: string;
  descricao: string;
  observacoes: string;
  imagens: File[];
}

// Interface para FormErrors
interface FormErrors {
  titulo?: string;
  atividade?: string;
  responsavel?: string;
  cargo?: string;
  dataInicio?: string;
  dataFim?: string;
  local?: string;
  equipamento?: string;
  descricao?: string;
}

// Definição de temas
const themes = {
  dark: {
    backgroundColor: '#0f172a',
    cardBackground: '#1e293b',
    textColor: '#f1f5f9',
    textSecondary: '#94a3b8',
    borderColor: '#334155',
    inputBackground: '#1e293b',
    inputBorder: '#475569',
    buttonPrimary: 'bg-blue-600 hover:bg-blue-700',
    buttonSecondary: 'bg-slate-600 hover:bg-slate-700',
    shadowColor: 'rgba(0, 0, 0, 0.4)',
    accent: '#3b82f6',
  },
  light: {
    backgroundColor: '#f8fafc',
    cardBackground: '#ffffff',
    textColor: '#1e293b',
    textSecondary: '#64748b',
    borderColor: '#e2e8f0',
    inputBackground: '#ffffff',
    inputBorder: '#d1d5db',
    buttonPrimary: 'bg-blue-600 hover:bg-blue-700',
    buttonSecondary: 'bg-slate-100 hover:bg-slate-200 text-slate-700',
    shadowColor: 'rgba(0, 0, 0, 0.1)',
    accent: '#3b82f6',
  }
};

const CreateReportPage: React.FC = () => {
  const navigate = useNavigate();
  const [sidebarOpen, setSidebarOpen] = useState(true);
  const [theme, setTheme] = useState<'light' | 'dark'>('light');
  const [imagePreviews, setImagePreviews] = useState<ImagePreview[]>([]);
  const [equipments, setEquipments] = useState<Equipment[]>([]);
  const [locations, setLocations] = useState<string[]>([]);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitSuccess, setSubmitSuccess] = useState(false);
  const [submitError, setSubmitError] = useState('');
  const [currentStep, setCurrentStep] = useState(1);
  const totalSteps = 4;
  
  // Estado do formulário
  const [formData, setFormData] = useState<FormData>({
    titulo: '',
    atividade: '',
    responsavel: '',
    cargo: '',
    dataInicio: new Date(),
    dataFim: new Date(),
    local: '',
    equipamento: '',
    descricao: '',
    observacoes: '',
    imagens: [],
  });
  
  // Estado para erros de validação
  const [errors, setErrors] = useState<FormErrors>({});

  // Efeito para carregar o tema do localStorage
  useEffect(() => {
    const savedTheme = localStorage.getItem('dashboard-theme');
    if (savedTheme === 'dark' || savedTheme === 'light') {
      setTheme(savedTheme);
    }
  }, []);

  // Efeito para carregar equipamentos e locais da API
  useEffect(() => {
    const fetchEquipments = async () => {
      try {
        const response = await axios.get('http://localhost:3001/api/equipamentos');
        setEquipments(response.data);
      } catch (error) {
        console.error('Erro ao carregar equipamentos:', error);
        // Dados mock para desenvolvimento
        setEquipments([
          { id: 1, nome: 'Bomba Centrífuga 001', tipo: 'Bomba', status: 'Operacional', setor: 'Produção' },
          { id: 2, nome: 'Motor Elétrico 002', tipo: 'Motor', status: 'Manutenção', setor: 'Utilidades' },
          { id: 3, nome: 'Compressor 003', tipo: 'Compressor', status: 'Operacional', setor: 'Ar Comprimido' },
        ]);
      }
    };

    const fetchLocations = async () => {
      try {
        const response = await axios.get('http://localhost:3001/api/locais');
        setLocations(response.data);
      } catch (error) {
        console.error('Erro ao carregar locais:', error);
        // Dados mock para desenvolvimento
        setLocations([
          'Área de Produção',
          'Sala de Máquinas',
          'Pátio Externo',
          'Laboratório',
          'Almoxarifado'
        ]);
      }
    };

    fetchEquipments();
    fetchLocations();
  }, []);

  // Função para alternar o sidebar
  const toggleSidebar = () => {
    setSidebarOpen(!sidebarOpen);
  };

  // Função para lidar com mudanças nos campos do formulário
  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormData({
      ...formData,
      [name]: value
    });
    
    // Limpa o erro deste campo quando o usuário começa a digitar
    if (errors[name as keyof FormErrors]) {
      setErrors({
        ...errors,
        [name]: undefined
      });
    }
  };

  // Função para lidar com mudanças nas datas
  const handleDateChange = (date: Date | null, field: 'dataInicio' | 'dataFim') => {
    setFormData({
      ...formData,
      [field]: date
    });
    
    // Limpa o erro deste campo quando o usuário seleciona uma data
    if (errors[field]) {
      setErrors({
        ...errors,
        [field]: undefined
      });
    }
  };

  // Função para lidar com upload de imagens
  const handleImageUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = Array.from(e.target.files || []);
    
    files.forEach(file => {
      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (event) => {
          const preview: ImagePreview = {
            file,
            preview: event.target?.result as string
          };
          setImagePreviews(prev => [...prev, preview]);
          setFormData(prev => ({
            ...prev,
            imagens: [...prev.imagens, file]
          }));
        };
        reader.readAsDataURL(file);
      }
    });
    
    // Limpa o input
    e.target.value = '';
  };

  // Função para remover imagem
  const removeImage = (index: number) => {
    setImagePreviews(prev => prev.filter((_, i) => i !== index));
    setFormData(prev => ({
      ...prev,
      imagens: prev.imagens.filter((_, i) => i !== index)
    }));
  };

  // Função para validar o formulário
  const validateForm = (): boolean => {
    const newErrors: FormErrors = {};
    
    // Validação dos campos obrigatórios
    if (!formData.titulo.trim()) newErrors.titulo = 'O título é obrigatório';
    if (!formData.atividade.trim()) newErrors.atividade = 'A atividade é obrigatória';
    if (!formData.responsavel.trim()) newErrors.responsavel = 'O nome do responsável é obrigatório';
    if (!formData.cargo.trim()) newErrors.cargo = 'O cargo do responsável é obrigatório';
    if (!formData.dataInicio) newErrors.dataInicio = 'A data de início é obrigatória';
    if (!formData.dataFim) newErrors.dataFim = 'A data de término é obrigatória';
    if (!formData.local.trim()) newErrors.local = 'O local é obrigatório';
    if (!formData.equipamento.trim()) newErrors.equipamento = 'O equipamento é obrigatório';
    if (!formData.descricao.trim()) newErrors.descricao = 'A descrição é obrigatória';
    
    // Validação adicional para datas
    if (formData.dataInicio && formData.dataFim && formData.dataInicio > formData.dataFim) {
      newErrors.dataFim = 'A data de término deve ser posterior à data de início';
    }
    
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  // Função para lidar com o envio do formulário
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    // Valida o formulário antes de enviar
    if (!validateForm()) {
      // Rola para o primeiro campo com erro
      const firstErrorField = document.querySelector('[aria-invalid="true"]');
      if (firstErrorField) {
        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      return;
    }
    
    setIsSubmitting(true);
    setSubmitError('');
    
    try {
      // Cria um objeto FormData para enviar arquivos
      const formDataToSend = new FormData();
      
      // Adiciona os campos do formulário
      formDataToSend.append('titulo', formData.titulo);
      formDataToSend.append('atividade', formData.atividade);
      formDataToSend.append('responsavel', formData.responsavel);
      formDataToSend.append('cargo', formData.cargo);
      formDataToSend.append('dataInicio', formData.dataInicio ? format(formData.dataInicio, 'yyyy-MM-dd HH:mm:ss') : '');
      formDataToSend.append('dataFim', formData.dataFim ? format(formData.dataFim, 'yyyy-MM-dd HH:mm:ss') : '');
      formDataToSend.append('local', formData.local);
      formDataToSend.append('equipamento', formData.equipamento);
      formDataToSend.append('descricao', formData.descricao);
      formDataToSend.append('observacoes', formData.observacoes);
      
      // Adiciona as imagens
      formData.imagens.forEach((image, index) => {
        formDataToSend.append(`imagens[${index}]`, image);
      });
      
      // Envia para a API
      const response = await axios.post('http://localhost:3001/api/relatorios', formDataToSend, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });
      
      console.log('Relatório criado com sucesso:', response.data);
      setSubmitSuccess(true);
      
      // Redireciona após 2 segundos
      setTimeout(() => {
        navigate('/relatorios');
      }, 2000);
      
    } catch (error) {
      console.error('Erro ao criar relatório:', error);
      setSubmitError('Ocorreu um erro ao criar o relatório. Por favor, tente novamente.');
    } finally {
      setIsSubmitting(false);
    }
  };

  // Função para navegar entre etapas
  const nextStep = () => {
    if (currentStep < totalSteps) {
      setCurrentStep(currentStep + 1);
    }
  };

  const prevStep = () => {
    if (currentStep > 1) {
      setCurrentStep(currentStep - 1);
    }
  };

  // Componente de Input personalizado
  const CustomInput: React.FC<{
    label: string;
    name: string;
    type?: string;
    placeholder?: string;
    required?: boolean;
    value: string;
    onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
    error?: string;
    icon?: React.ReactNode;
  }> = ({ label, name, type = 'text', placeholder, required, value, onChange, error, icon }) => (
    <div className="space-y-2">
      <label 
        htmlFor={name} 
        className={`block text-sm font-semibold ${theme === 'dark' ? 'text-slate-200' : 'text-slate-700'}`}
      >
        {label} {required && <span className="text-red-500 ml-1">*</span>}
      </label>
      <div className="relative">
        {icon && (
          <div className={`absolute left-3 top-1/2 transform -translate-y-1/2 ${theme === 'dark' ? 'text-slate-400' : 'text-slate-500'}`}>
            {icon}
          </div>
        )}
        <input
          type={type}
          id={name}
          name={name}
          value={value}
          onChange={onChange}
          placeholder={placeholder}
          className={`
            w-full ${icon ? 'pl-10' : 'pl-4'} pr-4 py-3 text-base
            ${theme === 'dark' 
              ? 'bg-slate-800 border-slate-600 text-slate-100 placeholder-slate-400' 
              : 'bg-white border-slate-300 text-slate-900 placeholder-slate-500'
            }
            border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500
            transition-all duration-200 ease-in-out
            ${error ? 'border-red-500 ring-2 ring-red-200' : ''}
            hover:border-slate-400 focus:outline-none
          `}
          aria-invalid={error ? 'true' : 'false'}
        />
      </div>
      {error && (
        <p className="text-sm text-red-500 flex items-center gap-1">
          <FiX className="w-4 h-4" />
          {error}
        </p>
      )}
    </div>
  );

  // Componente de TextArea personalizado
  const CustomTextArea: React.FC<{
    label: string;
    name: string;
    placeholder?: string;
    required?: boolean;
    value: string;
    onChange: (e: React.ChangeEvent<HTMLTextAreaElement>) => void;
    error?: string;
    rows?: number;
  }> = ({ label, name, placeholder, required, value, onChange, error, rows = 4 }) => (
    <div className="space-y-2">
      <label 
        htmlFor={name} 
        className={`block text-sm font-semibold ${theme === 'dark' ? 'text-slate-200' : 'text-slate-700'}`}
      >
        {label} {required && <span className="text-red-500 ml-1">*</span>}
      </label>
      <textarea
        id={name}
        name={name}
        value={value}
        onChange={onChange}
        placeholder={placeholder}
        rows={rows}
        className={`
          w-full px-4 py-3 text-base resize-none
          ${theme === 'dark' 
            ? 'bg-slate-800 border-slate-600 text-slate-100 placeholder-slate-400' 
            : 'bg-white border-slate-300 text-slate-900 placeholder-slate-500'
          }
          border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500
          transition-all duration-200 ease-in-out
          ${error ? 'border-red-500 ring-2 ring-red-200' : ''}
          hover:border-slate-400 focus:outline-none
        `}
        aria-invalid={error ? 'true' : 'false'}
      />
      {error && (
        <p className="text-sm text-red-500 flex items-center gap-1">
          <FiX className="w-4 h-4" />
          {error}
        </p>
      )}
    </div>
  );

  // Componente de Select personalizado
  const CustomSelect: React.FC<{
    label: string;
    name: string;
    required?: boolean;
    value: string;
    onChange: (e: React.ChangeEvent<HTMLSelectElement>) => void;
    error?: string;
    options: string[];
    placeholder?: string;
  }> = ({ label, name, required, value, onChange, error, options, placeholder }) => (
    <div className="space-y-2">
      <label 
        htmlFor={name} 
        className={`block text-sm font-semibold ${theme === 'dark' ? 'text-slate-200' : 'text-slate-700'}`}
      >
        {label} {required && <span className="text-red-500 ml-1">*</span>}
      </label>
      <select
        id={name}
        name={name}
        value={value}
        onChange={onChange}
        className={`
          w-full px-4 py-3 text-base
          ${theme === 'dark' 
            ? 'bg-slate-800 border-slate-600 text-slate-100' 
            : 'bg-white border-slate-300 text-slate-900'
          }
          border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500
          transition-all duration-200 ease-in-out
          ${error ? 'border-red-500 ring-2 ring-red-200' : ''}
          hover:border-slate-400 focus:outline-none
        `}
        aria-invalid={error ? 'true' : 'false'}
      >
        {placeholder && <option value="">{placeholder}</option>}
        {options.map((option, index) => (
          <option key={index} value={option}>
            {option}
          </option>
        ))}
      </select>
      {error && (
        <p className="text-sm text-red-500 flex items-center gap-1">
          <FiX className="w-4 h-4" />
          {error}
        </p>
      )}
    </div>
  );

  // Renderizar conteúdo da etapa atual
  const renderStepContent = () => {
    switch (currentStep) {
      case 1:
        return (
          <div className="space-y-6">
            <div className="text-center mb-8">
              <div className={`inline-flex items-center justify-center w-16 h-16 rounded-full ${theme === 'dark' ? 'bg-blue-900' : 'bg-blue-100'} mb-4`}>
                <FiInfo className={`w-8 h-8 ${theme === 'dark' ? 'text-blue-400' : 'text-blue-600'}`} />
              </div>
              <h2 className={`text-2xl font-bold ${theme === 'dark' ? 'text-slate-100' : 'text-slate-900'} mb-2`}>
                Informações Básicas
              </h2>
              <p className={`text-base ${theme === 'dark' ? 'text-slate-400' : 'text-slate-600'}`}>
                Preencha os dados principais do relatório
              </p>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <div className="lg:col-span-2">
                <CustomInput
                  label="Título do Relatório"
                  name="titulo"
                  placeholder="Ex: Manutenção Preventiva"
                  required
                  value={formData.titulo}
                  onChange={handleChange}
                  error={errors.titulo}
                  icon={<FiClipboard className="w-5 h-5" />}
                />
              </div>

              <CustomInput
                label="Atividade Realizada"
                name="atividade"
                placeholder="Ex: Inspeção e Lubrificação"
                required
                value={formData.atividade}
                onChange={handleChange}
                error={errors.atividade}
                icon={<FiTool className="w-5 h-5" />}
              />

              <CustomInput
                label="Nome do Responsável"
                name="responsavel"
                placeholder="Ex: João Silva"
                required
                value={formData.responsavel}
                onChange={handleChange}
                error={errors.responsavel}
              />

              <div className="lg:col-span-2">
                <CustomInput
                  label="Cargo do Responsável"
                  name="cargo"
                  placeholder="Ex: Técnico de Manutenção"
                  required
                  value={formData.cargo}
                  onChange={handleChange}
                  error={errors.cargo}
                />
              </div>
            </div>
          </div>
        );

      case 2:
        return (
          <div className="space-y-6">
            <div className="text-center mb-8">
              <div className={`inline-flex items-center justify-center w-16 h-16 rounded-full ${theme === 'dark' ? 'bg-green-900' : 'bg-green-100'} mb-4`}>
                <FiClock className={`w-8 h-8 ${theme === 'dark' ? 'text-green-400' : 'text-green-600'}`} />
              </div>
              <h2 className={`text-2xl font-bold ${theme === 'dark' ? 'text-slate-100' : 'text-slate-900'} mb-2`}>
                Data e Hora
              </h2>
              <p className={`text-base ${theme === 'dark' ? 'text-slate-400' : 'text-slate-600'}`}>
                Defina o período de execução da atividade
              </p>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <div className="space-y-2">
                <label className={`block text-sm font-semibold ${theme === 'dark' ? 'text-slate-200' : 'text-slate-700'}`}>
                  Data e Hora de Início <span className="text-red-500 ml-1">*</span>
                </label>
                <DatePicker
                  selected={formData.dataInicio}
                  onChange={(date) => handleDateChange(date, 'dataInicio')}
                  showTimeSelect
                  timeFormat="HH:mm"
                  timeIntervals={15}
                  dateFormat="dd/MM/yyyy HH:mm"
                  locale="pt-BR"
                  className={`
                    w-full px-4 py-3 text-base
                    ${theme === 'dark' 
                      ? 'bg-slate-800 border-slate-600 text-slate-100' 
                      : 'bg-white border-slate-300 text-slate-900'
                    }
                    border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                    transition-all duration-200 ease-in-out
                    ${errors.dataInicio ? 'border-red-500 ring-2 ring-red-200' : ''}
                    hover:border-slate-400 focus:outline-none
                  `}
                />
                {errors.dataInicio && (
                  <p className="text-sm text-red-500 flex items-center gap-1">
                    <FiX className="w-4 h-4" />
                    {errors.dataInicio}
                  </p>
                )}
              </div>

              <div className="space-y-2">
                <label className={`block text-sm font-semibold ${theme === 'dark' ? 'text-slate-200' : 'text-slate-700'}`}>
                  Data e Hora de Término <span className="text-red-500 ml-1">*</span>
                </label>
                <DatePicker
                  selected={formData.dataFim}
                  onChange={(date) => handleDateChange(date, 'dataFim')}
                  showTimeSelect
                  timeFormat="HH:mm"
                  timeIntervals={15}
                  dateFormat="dd/MM/yyyy HH:mm"
                  locale="pt-BR"
                  className={`
                    w-full px-4 py-3 text-base
                    ${theme === 'dark' 
                      ? 'bg-slate-800 border-slate-600 text-slate-100' 
                      : 'bg-white border-slate-300 text-slate-900'
                    }
                    border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                    transition-all duration-200 ease-in-out
                    ${errors.dataFim ? 'border-red-500 ring-2 ring-red-200' : ''}
                    hover:border-slate-400 focus:outline-none
                  `}
                />
                {errors.dataFim && (
                  <p className="text-sm text-red-500 flex items-center gap-1">
                    <FiX className="w-4 h-4" />
                    {errors.dataFim}
                  </p>
                )}
              </div>
            </div>
          </div>
        );

      case 3:
        return (
          <div className="space-y-6">
            <div className="text-center mb-8">
              <div className={`inline-flex items-center justify-center w-16 h-16 rounded-full ${theme === 'dark' ? 'bg-purple-900' : 'bg-purple-100'} mb-4`}>
                <FiMapPin className={`w-8 h-8 ${theme === 'dark' ? 'text-purple-400' : 'text-purple-600'}`} />
              </div>
              <h2 className={`text-2xl font-bold ${theme === 'dark' ? 'text-slate-100' : 'text-slate-900'} mb-2`}>
                Local e Equipamento
              </h2>
              <p className={`text-base ${theme === 'dark' ? 'text-slate-400' : 'text-slate-600'}`}>
                Especifique onde e em qual equipamento foi realizada a atividade
              </p>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <CustomSelect
                label="Local"
                name="local"
                required
                value={formData.local}
                onChange={handleChange}
                error={errors.local}
                options={locations}
                placeholder="Selecione o local"
              />

              <CustomSelect
                label="Equipamento"
                name="equipamento"
                required
                value={formData.equipamento}
                onChange={handleChange}
                error={errors.equipamento}
                options={equipments.map(eq => eq.nome)}
                placeholder="Selecione o equipamento"
              />

              <div className="lg:col-span-2">
                <CustomTextArea
                  label="Descrição da Atividade"
                  name="descricao"
                  placeholder="Descreva detalhadamente a atividade realizada..."
                  required
                  value={formData.descricao}
                  onChange={handleChange}
                  error={errors.descricao}
                  rows={5}
                />
              </div>

              <div className="lg:col-span-2">
                <CustomTextArea
                  label="Observações Adicionais"
                  name="observacoes"
                  placeholder="Adicione observações, recomendações ou comentários..."
                  value={formData.observacoes}
                  onChange={handleChange}
                  rows={3}
                />
              </div>
            </div>
          </div>
        );

      case 4:
        return (
          <div className="space-y-6">
            <div className="text-center mb-8">
              <div className={`inline-flex items-center justify-center w-16 h-16 rounded-full ${theme === 'dark' ? 'bg-orange-900' : 'bg-orange-100'} mb-4`}>
                <FiImage className={`w-8 h-8 ${theme === 'dark' ? 'text-orange-400' : 'text-orange-600'}`} />
              </div>
              <h2 className={`text-2xl font-bold ${theme === 'dark' ? 'text-slate-100' : 'text-slate-900'} mb-2`}>
                Imagens
              </h2>
              <p className={`text-base ${theme === 'dark' ? 'text-slate-400' : 'text-slate-600'}`}>
                Adicione fotos para documentar a atividade realizada
              </p>
            </div>

            <div className="space-y-6">
              {/* Upload de imagens */}
              <div className="space-y-4">
                <label className={`block text-sm font-semibold ${theme === 'dark' ? 'text-slate-200' : 'text-slate-700'}`}>
                  Adicionar Imagens
                </label>
                
                <div 
                  className={`
                    border-2 border-dashed rounded-xl p-8 text-center cursor-pointer
                    transition-all duration-200 ease-in-out
                    ${theme === 'dark' 
                      ? 'border-slate-600 bg-slate-800 hover:border-slate-500 hover:bg-slate-700' 
                      : 'border-slate-300 bg-slate-50 hover:border-slate-400 hover:bg-slate-100'
                    }
                  `}
                  onClick={() => document.getElementById('imageUpload')?.click()}
                >
                  <FiPlus className={`w-12 h-12 mx-auto mb-4 ${theme === 'dark' ? 'text-slate-400' : 'text-slate-500'}`} />
                  <p className={`text-lg font-medium ${theme === 'dark' ? 'text-slate-300' : 'text-slate-700'} mb-2`}>
                    Clique para adicionar imagens
                  </p>
                  <p className={`text-sm ${theme === 'dark' ? 'text-slate-400' : 'text-slate-500'}`}>
                    PNG, JPG, JPEG até 10MB cada
                  </p>
                </div>

                <input
                  id="imageUpload"
                  type="file"
                  multiple
                  accept="image/*"
                  onChange={handleImageUpload}
                  className="hidden"
                />
              </div>

              {/* Preview das imagens */}
              {imagePreviews.length > 0 && (
                <div className="space-y-4">
                  <h3 className={`text-lg font-semibold ${theme === 'dark' ? 'text-slate-200' : 'text-slate-700'}`}>
                    Imagens Selecionadas ({imagePreviews.length})
                  </h3>
                  
                  <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    {imagePreviews.map((image, index) => (
                      <div key={index} className="relative group">
                        <div className={`
                          relative rounded-lg overflow-hidden
                          ${theme === 'dark' ? 'bg-slate-800' : 'bg-slate-100'}
                          aspect-square
                        `}>
                          <img
                            src={image.preview}
                            alt={`Preview ${index + 1}`}
                            className="w-full h-full object-cover"
                          />
                          <button
                            type="button"
                            onClick={() => removeImage(index)}
                            className="
                              absolute top-2 right-2 w-8 h-8 
                              bg-red-500 hover:bg-red-600 
                              text-white rounded-full 
                              flex items-center justify-center
                              opacity-0 group-hover:opacity-100
                              transition-opacity duration-200
                            "
                          >
                            <FiX className="w-4 h-4" />
                          </button>
                        </div>
                        <p className={`
                          mt-2 text-xs truncate
                          ${theme === 'dark' ? 'text-slate-400' : 'text-slate-600'}
                        `}>
                          {image.file.name}
                        </p>
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>
          </div>
        );

      default:
        return null;
    }
  };

  return (
    // Ajustando o layout principal
    <div 
      className="min-h-screen transition-colors duration-300 flex flex-col"
      style={{ backgroundColor: themes[theme].backgroundColor }}
    >
      {/* TopBar com ajustes para mobile */}
      <TopBar 
        toggleSidebar={toggleSidebar} 
        theme={theme} 
        toggleTheme={() => setTheme(theme === 'light' ? 'dark' : 'light')}
        onLogout={() => {}} 
        isSidebarOpen={sidebarOpen}
      />
      
      {/* Sidebar com ajustes para mobile */}
      <Sidebar 
        isOpen={sidebarOpen} 
        toggleSidebar={toggleSidebar}
      />
      
      {/* Container principal com ajustes de responsividade */}
      <div 
        className={`
          flex-1 transition-all duration-300 ease-in-out
          ${sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'}
          mt-16 px-4 sm:px-6 lg:px-8 pb-8
          max-w-7xl mx-auto w-full
        `}
      >
        {/* Cabeçalho do formulário */}
        <div className="mb-6 md:mb-8">
          <h1 className={`text-2xl md:text-3xl font-bold ${theme === 'dark' ? 'text-slate-100' : 'text-slate-900'} mb-2`}>
            Novo Relatório
          </h1>
          <p className={`text-base md:text-lg ${theme === 'dark' ? 'text-slate-400' : 'text-slate-600'}`}>
            Preencha o formulário abaixo para criar um novo relatório de manutenção
          </p>
        </div>
    
        {/* Indicador de Progresso Otimizado */}
        <div className="mb-6 md:mb-8 bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm">
          <div className="flex items-center justify-between mb-2">
            <span className={`text-sm font-medium ${theme === 'dark' ? 'text-slate-300' : 'text-slate-700'}`}>
              Etapa {currentStep} de {totalSteps}
            </span>
            <span className={`text-sm font-medium ${theme === 'dark' ? 'text-slate-300' : 'text-slate-700'}`}>
              {Math.round((currentStep / totalSteps) * 100)}% concluído
            </span>
          </div>
          <div className="relative pt-1">
            <div className={`overflow-hidden h-2 text-xs flex rounded-full ${theme === 'dark' ? 'bg-slate-700' : 'bg-slate-200'}`}>
              <div 
                className="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-600"
                style={{ width: `${(currentStep / totalSteps) * 100}%` }}
              />
            </div>
          </div>
        </div>
    
        {/* Formulário com layout otimizado */}
        <form onSubmit={handleSubmit} className="space-y-6">
          <div 
            className={`
              rounded-xl shadow-lg border
              ${theme === 'dark' ? 'bg-slate-800 border-slate-700' : 'bg-white border-slate-200'}
              overflow-hidden
            `}
          >
            {/* Container do conteúdo com padding responsivo */}
            <div className="p-4 sm:p-6 md:p-8">
              {renderStepContent()}
            </div>
    
            {/* Barra de navegação fixa na parte inferior em mobile */}
            <div className={`
              sticky bottom-0 left-0 right-0
              p-4 sm:p-6
              border-t
              ${theme === 'dark' ? 'bg-slate-800 border-slate-700' : 'bg-white border-slate-200'}
              flex flex-col sm:flex-row justify-between items-center gap-4
            `}>
              <div className="w-full sm:w-auto order-2 sm:order-1">
                {currentStep > 1 && (
                  <button
                    type="button"
                    onClick={prevStep}
                    className={`
                      w-full sm:w-auto
                      inline-flex items-center justify-center px-6 py-3 text-base font-medium rounded-xl
                      transition-all duration-200 ease-in-out
                      ${theme === 'dark' 
                        ? 'bg-slate-700 text-slate-200 hover:bg-slate-600 border-slate-600' 
                        : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border-slate-300'
                      }
                      border focus:outline-none focus:ring-2 focus:ring-blue-500
                    `}
                  >
                    <FiChevronLeft className="w-5 h-5 mr-2" />
                    Anterior
                  </button>
                )}
              </div>
    
              <div className="w-full sm:w-auto order-1 sm:order-2">
                {currentStep < totalSteps ? (
                  <button
                    type="button"
                    onClick={nextStep}
                    className="
                      w-full sm:w-auto
                      inline-flex items-center justify-center px-6 py-3 text-base font-medium rounded-xl
                      bg-blue-600 text-white hover:bg-blue-700
                      transition-all duration-200 ease-in-out
                      focus:outline-none focus:ring-2 focus:ring-blue-500
                    "
                  >
                    Próximo
                    <FiChevronRight className="w-5 h-5 ml-2" />
                  </button>
                ) : (
                  <button
                    type="submit"
                    disabled={isSubmitting}
                    className={`
                      w-full sm:w-auto
                      inline-flex items-center justify-center px-8 py-3 text-base font-medium rounded-xl
                      transition-all duration-200 ease-in-out
                      focus:outline-none focus:ring-2 focus:ring-blue-500
                      ${isSubmitting 
                        ? 'bg-slate-400 text-slate-200 cursor-not-allowed' 
                        : 'bg-blue-600 text-white hover:bg-blue-700 hover:shadow-lg'
                      }
                    `}
                  >
                    {isSubmitting ? (
                      <>
                        <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                        Salvando...
                      </>
                    ) : (
                      <>
                        <FiSave className="w-5 h-5 mr-2" />
                        Salvar Relatório
                      </>
                    )}
                  </button>
                )}
              </div>
            </div>
          </div>
        </form>
    
        {/* Mensagens de Sucesso/Erro */}
        {submitSuccess && (
          <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div className={`
              rounded-xl p-6 max-w-sm w-full mx-auto text-center
              ${theme === 'dark' ? 'bg-slate-800' : 'bg-white'}
            `}>
              <FiCheckCircle className="w-12 h-12 text-green-500 mx-auto mb-4" />
              <h3 className={`text-xl font-bold ${theme === 'dark' ? 'text-slate-100' : 'text-slate-900'} mb-2`}>
                Relatório Criado!
              </h3>
              <p className={`${theme === 'dark' ? 'text-slate-400' : 'text-slate-600'}`}>
                Redirecionando para a lista de relatórios...
              </p>
            </div>
          </div>
        )}
    
        {submitError && (
          <div className="fixed bottom-4 inset-x-4 sm:right-4 sm:left-auto sm:max-w-md z-50">
            <div className="bg-red-500 text-white p-4 rounded-xl shadow-lg">
              <div className="flex items-center">
                <FiX className="w-5 h-5 mr-2 flex-shrink-0" />
                <span className="font-medium">{submitError}</span>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default CreateReportPage;