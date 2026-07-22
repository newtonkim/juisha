<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import MaterialIcon from '@/components/focusflow/MaterialIcon.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        title: 'Create your account',
        description:
            'Add one task, press start, and your first focus session begins. That’s the whole onboarding.',
    },
});

const labelClass =
    'font-mono text-[11px] font-medium tracking-[0.18em] text-on-surface-variant uppercase';
const inputClass =
    'h-11 rounded-xl border-outline-variant bg-surface-container-lowest px-3.5 text-on-surface shadow-none placeholder:text-outline focus-visible:border-secondary-fx focus-visible:ring-[3px] focus-visible:ring-secondary-fx/20';
</script>

<template>
    <Head title="Register" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-7"
    >
        <div class="grid gap-5">
            <div class="grid gap-2">
                <Label for="name" :class="labelClass">Name</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    name="name"
                    placeholder="Full name"
                    :class="inputClass"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email" :class="labelClass">Email address</Label>
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="2"
                    autocomplete="email"
                    name="email"
                    placeholder="email@example.com"
                    :class="inputClass"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="password" :class="labelClass">Password</Label>
                <PasswordInput
                    id="password"
                    required
                    :tabindex="3"
                    autocomplete="new-password"
                    name="password"
                    placeholder="Password"
                    :passwordrules="passwordRules"
                    :class="inputClass"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation" :class="labelClass"
                    >Confirm password</Label
                >
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Confirm password"
                    :passwordrules="passwordRules"
                    :class="inputClass"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <button
                type="submit"
                class="group mt-2 inline-flex h-12 w-full items-center justify-center gap-2 rounded-full bg-primary-fx font-display text-base font-bold text-on-primary-fx shadow-lg shadow-primary-fx/20 transition hover:bg-primary-container-fx active:scale-[0.98] disabled:pointer-events-none disabled:opacity-60"
                tabindex="5"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                Start focusing — it's free
                <MaterialIcon
                    v-if="!processing"
                    name="arrow_forward"
                    class="!text-xl transition-transform group-hover:translate-x-0.5"
                />
            </button>

            <p
                class="text-center font-mono text-[10px] tracking-[0.2em] text-outline uppercase"
            >
                No card · No setup
            </p>
        </div>

        <div
            class="border-t border-outline-variant/60 pt-5 text-center text-sm text-on-surface-variant"
        >
            Already have an account?
            <Link
                :href="login()"
                class="font-semibold text-secondary-fx underline-offset-4 transition hover:underline"
                :tabindex="6"
                >Log in</Link
            >
        </div>
    </Form>
</template>
