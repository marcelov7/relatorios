<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Excluir Conta
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Uma vez que sua conta for excluída, todos os seus dados serão permanentemente removidos. Antes de excluir, faça o download de qualquer informação que deseje manter.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion" class="dark:bg-red-600 dark:hover:bg-red-700">Excluir Conta</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2
                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >
                    Tem certeza que deseja excluir sua conta?
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Todos os seus dados serão removidos permanentemente. Por favor, digite sua senha para confirmar.
                </p>

                <div class="mt-6">
                    <InputLabel
                        for="password"
                        value="Senha"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4 border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 shadow-sm"
                        placeholder="Senha"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal" class="dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3 dark:bg-red-600 dark:hover:bg-red-700"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Excluir Conta
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
