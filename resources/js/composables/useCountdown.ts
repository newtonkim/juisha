import { computed, onBeforeUnmount, ref } from 'vue';

export function useCountdown(
    options: { onFinish?: () => void; onWarning?: () => void } = {},
) {
    const remaining = ref(0);
    const running = ref(false);
    let handle: number | undefined;
    let target = 0;
    let warned = false;

    function tick() {
        remaining.value = Math.max(0, Math.round((target - Date.now()) / 1000));

        if (!warned && remaining.value <= 60 && remaining.value > 0) {
            warned = true;
            options.onWarning?.();
        }

        if (remaining.value <= 0) {
            stop();
            options.onFinish?.();
        }
    }
    function stop() {
        if (handle) {
            window.clearInterval(handle);
        }

        handle = undefined;
        running.value = false;
    }
    function start(seconds: number) {
        target = Date.now() + seconds * 1000;
        warned = seconds <= 60;
        stop();
        running.value = true;
        handle = window.setInterval(tick, 250);
        tick();
    }
    function pause() {
        if (running.value) {
            tick();
            stop();
        }
    }
    function resume() {
        if (!running.value && remaining.value > 0) {
            start(remaining.value);
        }
    }

    const display = computed(
        () =>
            `${String(Math.floor(remaining.value / 60)).padStart(2, '0')}:${String(remaining.value % 60).padStart(2, '0')}`,
    );
    onBeforeUnmount(stop);

    return { remaining, running, display, start, pause, resume };
}
