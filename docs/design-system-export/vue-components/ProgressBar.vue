<script setup lang="ts">
import { computed } from "vue";

const props = withDefaults(
  defineProps<{
    value: number;
    max?: number;
    label?: string;
    class?: string;
  }>(),
  {
    max: 100,
    label: "Progress",
    class: "",
  },
);

const safeMax = computed(() => (props.max > 0 ? props.max : 100));
const clampedValue = computed(() =>
  Math.min(Math.max(props.value, 0), safeMax.value),
);
const percent = computed(() => `${(clampedValue.value / safeMax.value) * 100}%`);
</script>

<template>
  <div
    role="progressbar"
    :aria-label="label"
    :aria-valuemin="0"
    :aria-valuemax="safeMax"
    :aria-valuenow="clampedValue"
    :class="[
      'h-2 w-[60px] overflow-hidden rounded-full border border-brand-neutral-900 bg-brand-neutral-0',
      props.class,
    ]"
  >
    <div
      class="h-full rounded-full bg-brand-neutral-900 transition-[width] duration-150 ease-in-out"
      :style="{ width: percent }"
    />
  </div>
</template>
