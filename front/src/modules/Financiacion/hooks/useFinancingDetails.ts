import { ref, computed, onMounted, watch } from 'vue';
import type { FinancingDetailsInterface } from '../types/VehicleInterface';
import FinancingService from "@/modules/Financiacion/services";
import { alertWithToast } from "@/utils/toast";
import { FormatCurrency, unFormatCurrency } from '@/utils/formatCurrency';

export function useFinancingDetails(props: { financing: FinancingDetailsInterface }, emit: any) {
  const docFile = ref('');
  const statusMora = ref(false);
  const loadingMora = ref(false);
  const loadingInvoice = ref(false);
  const showRequirementsModal = ref(false);

  const isEditingPlaca = ref(false);
  const newPlaca = ref('');
  const savingPlaca = ref(false);

  // Financial Editing State
  const financeForm = ref({
    financing_price: '',
    interes_price: '',
    price_diario: '',
    price_semanal: '',
    price_quincenal: '',
    price_mensual: '',
    meses: 0,
    cuotas: 0,
    interes_porcent: 0,
    plan: '' as 'Diario' | 'Semanal' | 'Quincenal' | 'Mensual' | '',
    status: ''
  });

  const isEditingInterest = ref(false);
  const isEditingFinancingPrice = ref(false);
  const isEditingInteresPrice = ref(false);
  const isEditingInstallments = ref(false);
  const isEditingSuggested = ref(false);
  const isEditingServices = ref(false);
  const isEditingPlan = ref(false);
  const isEditingStatus = ref(false);
  const savingFinance = ref(false);
  const savingServices = ref(false);

  const servicesForm = ref<Array<{
    id: number
    name: string
    is_included: boolean
    price: string | number
  }>>([]);

  // Local copy for optimistic updates
  const financingData = ref({ ...props.financing });

  watch(() => props.financing, (newVal) => {
    financingData.value = { ...newVal };
  }, { deep: true });

  const openPlacaModal = () => {
    if (financingData.value.estado !== 'activa') return;
    newPlaca.value = financingData.value?.vehiculo_placa || '';
    isEditingPlaca.value = true;
  };

  const cancelEditPlaca = () => {
    isEditingPlaca.value = false;
  };

  const updatePlaca = async () => {
    if (!newPlaca.value) {
      alertWithToast('La placa no puede estar vacía', 'error');
      return;
    }
    savingPlaca.value = true;
    try {
      const response = await FinancingService.updatePlaca(String(financingData.value.id), { plate: newPlaca.value });
      if (response && response.data) {
        financingData.value.vehiculo_placa = response.data.plate;
        alertWithToast('Placa actualizada correctamente', 'success');
        isEditingPlaca.value = false;
      }
    } catch (error) {
      alertWithToast('Error al actualizar la placa', 'error');
    } finally {
      savingPlaca.value = false;
    }
  };

  const activarMora = async () => {
    loadingMora.value = true;
    try {
      const response = await FinancingService.moraEstatus(financingData.value?.id || '');
      if (response && response.data) {
        statusMora.value = response.data.status;
        alertWithToast(response.data.message, 'success');
      }
    } catch (error) {
      alertWithToast('Error al activar la mora', 'error');
    } finally {
      loadingMora.value = false;
    }
  };

  const handleDownloadInvoice = async () => {
    if (!financingData.value.id) {
      alertWithToast('No hay una financiación vinculada', 'error');
      return;
    }
    try {
      loadingInvoice.value = true;
      await FinancingService.downloadInvoice(financingData.value.id, financingData.value.codigo);
    } catch (e) {
      alertWithToast('No se pudo generar la factura. Intenta de nuevo.', 'error');
    } finally {
      loadingInvoice.value = false;
    }
  };

  const handleUpdateRequirements = async (formData: FormData) => {
    try {
      await FinancingService.updateRequirements(String(financingData.value.id), formData);
      alertWithToast('Recaudos actualizados correctamente', 'success');
      showRequirementsModal.value = false;
      emit('update');
    } catch (e) {
      alertWithToast('Error al actualizar recaudos', 'error');
    }
  };

  const isEditingFinance = computed(() =>
    isEditingInterest.value ||
    isEditingFinancingPrice.value ||
    isEditingInteresPrice.value ||
    isEditingInstallments.value ||
    isEditingSuggested.value ||
    isEditingStatus.value
  );

  const startEditingFinance = (field: string = 'all') => {
    financeForm.value = {
      financing_price: FormatCurrency(financingData.value.cost_inicial || 0),
      interes_price: FormatCurrency(financingData.value.intereses || 0),
      price_diario: FormatCurrency(financingData.value.diario || 0),
      price_semanal: FormatCurrency(financingData.value.semanal || 0),
      price_quincenal: FormatCurrency(financingData.value.quincenal || 0),
      price_mensual: FormatCurrency(financingData.value.mensual || 0),
      meses: financingData.value.meses || 0,
      cuotas: financingData.value.cuotas || 0,
      interes_porcent: financingData.value.interes_porcent || 0,
      plan: financingData.value.plan || '',
      status: financingData.value.estado || ''
    };

    if (field === 'interest') isEditingInterest.value = true;
    if (field === 'financing_price') isEditingFinancingPrice.value = true;
    if (field === 'interes_price') isEditingInteresPrice.value = true;
    if (field === 'installments') isEditingInstallments.value = true;
    if (field === 'suggested') isEditingSuggested.value = true;
    if (field === 'plan') isEditingPlan.value = true;

    if (field === 'all') {
      isEditingInterest.value = true;
      isEditingFinancingPrice.value = true;
      isEditingInteresPrice.value = true;
      isEditingInstallments.value = true;
      isEditingSuggested.value = true;
      isEditingServices.value = true;
      isEditingPlan.value = true;
      isEditingStatus.value = true;
    }

    if (field === 'status') isEditingStatus.value = true;
    if (field === 'plan') isEditingPlan.value = true;
  };

  const startEditingServices = () => {
    servicesForm.value = (financingData.value.services || []).map(s => ({
      ...s,
      price: FormatCurrency(s.price || 0)
    }));
    isEditingServices.value = true;
  };

  const toggleService = (id: number) => {
    const service = servicesForm.value.find(s => s.id === id);
    if (service) {
      service.is_included = !service.is_included;
    }
  };

  // Computed properties for live calculation (unformatting for math)
  const computedPrecioVehiculo = computed(() => {
    if (isEditingFinance.value) {
      return (financingData.value.inicial || 0) + Number(unFormatCurrency(financeForm.value.financing_price));
    }
    return financingData.value.precio || 0;
  });

  const computedServicesTotal = computed(() => {
    if (isEditingServices.value) {
      return servicesForm.value
        .filter(s => s.is_included)
        .reduce((sum, s) => sum + Number(unFormatCurrency(String(s.price))), 0);
    }
    return (financingData.value.services || [])
      .filter(s => s.is_included)
      .reduce((sum, s) => sum + (s.price || 0), 0);
  });

  const computedNetoTotal = computed(() => {
    const fPrice = isEditingFinance.value ? Number(unFormatCurrency(financeForm.value.financing_price)) : (financingData.value.cost_inicial || 0);
    const iPrice = isEditingFinance.value ? Number(unFormatCurrency(financeForm.value.interes_price)) : (financingData.value.intereses || 0);
    
    return fPrice + iPrice + computedServicesTotal.value;
  });

  const computedFaltante = computed(() => {
    if (isEditingFinance.value) {
      return Math.max(computedNetoTotal.value - (financingData.value.pagado || 0), 0);
    }
    return Math.max((financingData.value.precio_final || 0) - (financingData.value.pagado || 0), 0);
  });

  const liveFinancing = computed(() => {
    if (!isEditingFinance.value && !isEditingServices.value && !isEditingPlan.value) return financingData.value;

    return {
      ...financingData.value,
      cost_inicial: (isEditingFinance.value || isEditingPlan.value) ? Number(unFormatCurrency(financeForm.value.financing_price)) : (financingData.value.cost_inicial || 0),
      intereses: (isEditingFinance.value || isEditingPlan.value) ? Number(unFormatCurrency(financeForm.value.interes_price)) : (financingData.value.intereses || 0),
      mensual: (isEditingFinance.value || isEditingPlan.value) ? Number(unFormatCurrency(financeForm.value.price_mensual)) : (financingData.value.mensual || 0),
      quincenal: (isEditingFinance.value || isEditingPlan.value) ? Number(unFormatCurrency(financeForm.value.price_quincenal)) : (financingData.value.quincenal || 0),
      semanal: (isEditingFinance.value || isEditingPlan.value) ? Number(unFormatCurrency(financeForm.value.price_semanal)) : (financingData.value.semanal || 0),
      diario: (isEditingFinance.value || isEditingPlan.value) ? Number(unFormatCurrency(financeForm.value.price_diario)) : (financingData.value.diario || 0),
      meses: (isEditingFinance.value || isEditingPlan.value) ? financeForm.value.meses : (financingData.value.meses || 0),
      cuotas: (isEditingFinance.value || isEditingPlan.value) ? financeForm.value.cuotas : (financingData.value.cuotas || 0),
      interes_porcent: (isEditingFinance.value || isEditingPlan.value) ? financeForm.value.interes_porcent : (financingData.value.interes_porcent || 0),
      plan: (isEditingFinance.value || isEditingPlan.value) ? financeForm.value.plan : (financingData.value.plan || null),
      estado: isEditingStatus.value ? financeForm.value.status : (financingData.value.estado || 'activa'),
      
      // Servicios editados reactivamente
      services: isEditingServices.value ? servicesForm.value.map(s => ({
        ...s,
        price: Number(unFormatCurrency(String(s.price)))
      })) : (financingData.value.services || []),

      precio: computedPrecioVehiculo.value,
      precio_final: computedNetoTotal.value,
      faltante: computedFaltante.value
    };
  });

  const saveFinance = async (type: string = 'all') => {
    savingFinance.value = true;
    try {
      const payload: any = {
        financing_price: unFormatCurrency(financeForm.value.financing_price),
        interes_price: unFormatCurrency(financeForm.value.interes_price),
        price_diario: unFormatCurrency(financeForm.value.price_diario),
        price_semanal: unFormatCurrency(financeForm.value.price_semanal),
        price_quincenal: unFormatCurrency(financeForm.value.price_quincenal),
        price_mensual: unFormatCurrency(financeForm.value.price_mensual),
        months: financeForm.value.meses,
        installments: financeForm.value.cuotas,
        interes_porcent: financeForm.value.interes_porcent,
        plan: financeForm.value.plan,
        status: isEditingStatus.value ? financeForm.value.status : undefined,
      };

      // Add final_price for the backend to update it too
      payload.final_price = Number(payload.financing_price) + Number(payload.interes_price);

      await FinancingService.updateFinanceDetails(String(financingData.value.id), payload);

      // Optimistically update local data
      financingData.value = {
        ...financingData.value,
        interes_porcent: Number(payload.interes_porcent),
        precio_final: computedNetoTotal.value,
        cost_inicial: Number(payload.financing_price),
        intereses: Number(payload.interes_price),
        diario: Number(payload.price_diario),
        semanal: Number(payload.price_semanal),
        quincenal: Number(payload.price_quincenal),
        mensual: Number(payload.price_mensual),
        meses: Number(payload.months),
        cuotas: Number(payload.installments),
        plan: payload.plan
      };

      alertWithToast('Valores actualizados correctamente', 'success');

      // Close all editing states
      isEditingInterest.value = false;
      isEditingFinancingPrice.value = false;
      isEditingInteresPrice.value = false;
      isEditingInstallments.value = false;
      isEditingSuggested.value = false;
      isEditingPlan.value = false;
      isEditingStatus.value = false;

      emit('update');
    } catch (error) {
      alertWithToast('Error al actualizar los valores', 'error');
    } finally {
      savingFinance.value = false;
    }
  };

  const saveServices = async () => {
    savingServices.value = true;
    try {
      const payload = {
        services: servicesForm.value.map(s => ({
          id: s.id,
          is_included: s.is_included,
          price: unFormatCurrency(String(s.price))
        }))
      };

      await FinancingService.updateFinanceDetails(String(financingData.value.id), payload);

      // Optimistically update local data
      financingData.value = {
        ...financingData.value,
        services: servicesForm.value.map(s => ({
          ...s,
          price: Number(unFormatCurrency(String(s.price)))
        })),
        precio_final: computedNetoTotal.value
      };

      alertWithToast('Servicios actualizados correctamente', 'success');
      isEditingServices.value = false;
      emit('update');
    } catch (error) {
      alertWithToast('Error al actualizar los servicios', 'error');
    } finally {
      savingServices.value = false;
    }
  };

  const reCalculatePrices = (trigger?: string) => {
    const financingPrice = Number(unFormatCurrency(financeForm.value.financing_price));
    const interestPrice = Number(unFormatCurrency(financeForm.value.interes_price));
    const total = financingPrice + interestPrice;
    const months = Number(financeForm.value.meses);
    const plan = financeForm.value.plan || financingData.value.plan;

    if (months > 0) {
      if (trigger !== 'cuotas') {
        // Calculate total cuotas based on plan like backend match
        let totalCuotas = months;
        if (plan === 'Diario') totalCuotas = months * 4 * 6;
        else if (plan === 'Semanal') totalCuotas = months * 4;
        else if (plan === 'Quincenal') totalCuotas = months * 2;
        financeForm.value.cuotas = totalCuotas;
      }

      const activeCuotas = Math.max(Number(financeForm.value.cuotas), 1);
      const precioPorCuota = total / activeCuotas;

      let month = (plan === 'Mensual') ? precioPorCuota : (total / months);
      let quincenal = (plan === 'Quincenal') ? precioPorCuota : (month / 2);
      let weekly = (plan === 'Semanal') ? precioPorCuota : (month / 4);
      let daily = (plan === 'Diario') ? precioPorCuota : (weekly / 6);

      const formatWithComma = (val: number) => FormatCurrency(String(val.toFixed(2)).replace('.', ','));

      financeForm.value.price_mensual = formatWithComma(month);
      financeForm.value.price_quincenal = formatWithComma(quincenal);
      financeForm.value.price_semanal = formatWithComma(weekly);
      financeForm.value.price_diario = formatWithComma(daily);
    }
  };

  const handlePriceInput = (field: keyof typeof financeForm.value, event: Event) => {
    const input = event.target as HTMLInputElement;
    if (field === 'interes_porcent') {
      financeForm.value.interes_porcent = Number(input.value);
    } else {
      (financeForm.value as any)[field] = FormatCurrency(input.value);
    }

    if (field === 'financing_price' || field === 'interes_price' || field === 'interes_porcent') {
      if (field === 'interes_porcent') {
        const financingPrice = Number(unFormatCurrency(financeForm.value.financing_price));
        const interes = Number(financeForm.value.interes_porcent);
        financeForm.value.interes_price = FormatCurrency(financingPrice * interes);
      }
      reCalculatePrices();
    }
  };

  const handleMesesInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    financeForm.value.meses = Number(input.value);
    reCalculatePrices();
  };

  onMounted(() => {
    statusMora.value = props.financing?.moraEstatus || false;
  });

  return {
    docFile,
    statusMora,
    loadingMora,
    loadingInvoice,
    showRequirementsModal,
    isEditingPlaca,
    newPlaca,
    savingPlaca,
    isEditingInterest,
    isEditingFinancingPrice,
    isEditingInteresPrice,
    isEditingInstallments,
    isEditingSuggested,
    isEditingServices,
    isEditingPlan,
    isEditingStatus,
    isEditingFinance,
    savingFinance,
    savingServices,
    financeForm,
    servicesForm,
    openPlacaModal,
    cancelEditPlaca,
    updatePlaca,
    activarMora,
    handleDownloadInvoice,
    handleUpdateRequirements,
    startEditingFinance,
    saveFinance,
    startEditingServices,
    toggleService,
    saveServices,
    computedPrecioVehiculo,
    computedNetoTotal,
    computedFaltante,
    handlePriceInput,
    handleMesesInput,
    reCalculatePrices,
    liveFinancing
  };
}
