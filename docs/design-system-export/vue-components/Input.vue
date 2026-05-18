<script setup lang="ts">
import { computed, useId } from "vue";

const props = withDefaults(
  defineProps<{
    id?: string;
    name?: string;
    label?: string;
    modelValue?: string | number;
    type?: string;
    placeholder?: string;
    validationMessage?: string;
    disabled?: boolean;
    class?: string;
  }>(),
  {
    type: "text",
    disabled: false,
    class: "",
  },
);

const emit = defineEmits<{
  "update:modelValue": [value: string];
}>();

const generatedId = useId();
const inputId = computed(() => props.id ?? props.name ?? generatedId);
const validationId = computed(() =>
  props.validationMessage ? `${inputId.value}-validation` : undefined,
);

const classes = computed(() =>
  [
    "h-[54px] w-full rounded-md border border-brand-neutral-900 bg-brand-neutral-0 px-4 text-preset-5 text-brand-neutral-900 placeholder:text-brand-neutral-600",
    "transition-[border-color,box-shadow] duration-150 ease-in-out",
    "hover:shadow-[2px_2px_0_0_var(--color-brand-neutral-900)]",
    "focus:border-brand-blue-600 focus:outline-none focus:shadow-[2px_2px_0_0_var(--color-brand-blue-600)]",
    "disabled:cursor-not-allowed disabled:opacity-50",
    props.validationMessage
      ? "border-brand-pink-700 shadow-[2px_2px_0_0_var(--color-brand-pink-700)] hover:shadow-[2px_2px_0_0_var(--color-brand-pink-700)] focus:border-brand-pink-700 focus:shadow-[2px_2px_0_0_var(--color-brand-pink-700)]"
      : "",
    props.class,
  ]
    .filter(Boolean)
    .join(" "),
);
</script>

<template>
  <div class="flex w-full flex-col gap-2">
    <label
      v-if="label"
      :for="inputId"
      class="text-preset-4 text-brand-neutral-900"
    >
      {{ label }}
    </label>

    <input
      :id="inputId"
      :name="name"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :aria-describedby="validationId"
      :aria-invalid="validationMessage ? true : undefined"
      :class="classes"
      @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
    />

    <p
      v-if="validationMessage"
      :id="validationId"
      class="flex items-center gap-2 text-preset-5 text-brand-pink-700"
    >
      <span
        aria-hidden="true"
        class="size-[14px] shrink-0 bg-brand-neutral-900 [mask:url('/assets/alert.svg')_center/contain_no-repeat]"
      />
      <span>{{ validationMessage }}</span>
    </p>
  </div>
</template>
