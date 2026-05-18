<script setup lang="ts">
import { computed, useId } from "vue";

const props = withDefaults(
  defineProps<{
    id?: string;
    name?: string;
    label?: string;
    modelValue?: boolean;
    disabled?: boolean;
    class?: string;
  }>(),
  {
    modelValue: false,
    disabled: false,
    class: "",
  },
);

const emit = defineEmits<{
  "update:modelValue": [value: boolean];
}>();

const generatedId = useId();
const inputId = computed(() => props.id ?? props.name ?? generatedId);

const labelClasses = computed(() =>
  [
    "inline-flex cursor-pointer items-center gap-2 text-preset-5 text-brand-neutral-900",
    props.disabled ? "cursor-not-allowed opacity-50" : "",
    props.class,
  ]
    .filter(Boolean)
    .join(" "),
);
</script>

<template>
  <label :for="inputId" :class="labelClasses">
    <input
      :id="inputId"
      :name="name"
      type="checkbox"
      class="peer sr-only"
      :checked="modelValue"
      :disabled="disabled"
      @change="emit('update:modelValue', ($event.target as HTMLInputElement).checked)"
    />

    <span
      aria-hidden="true"
      class="flex size-5 shrink-0 items-center justify-center rounded-sm border border-brand-neutral-900 bg-brand-neutral-0 transition-[background-color,box-shadow] duration-150 ease-in-out peer-hover:shadow-[1px_1px_0_0_var(--color-brand-neutral-900)] peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-brand-blue-600 peer-checked:bg-brand-yellow-500 peer-checked:bg-[url('/assets/check.svg')] peer-checked:bg-size-[12px_10px] peer-checked:bg-center peer-checked:bg-no-repeat"
    />

    <span v-if="label">{{ label }}</span>
  </label>
</template>
