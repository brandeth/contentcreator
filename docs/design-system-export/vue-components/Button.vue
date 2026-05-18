<script setup lang="ts">
import { computed } from "vue";

type ButtonVariant =
  | "primary"
  | "secondary"
  | "outline"
  | "base-primary"
  | "base-secondary";

const props = withDefaults(
  defineProps<{
    variant?: ButtonVariant;
    type?: "button" | "submit" | "reset";
    disabled?: boolean;
    class?: string;
  }>(),
  {
    variant: "primary",
    type: "button",
    disabled: false,
    class: "",
  },
);

const raisedButtonStateClasses =
  "shadow-[inset_0_0_0_1px_var(--color-brand-neutral-900),2px_2px_0_0_var(--color-brand-neutral-900)] hover:not-disabled:shadow-[inset_0_0_0_1px_var(--color-brand-neutral-900),4px_4px_0_0_var(--color-brand-neutral-900)] focus-visible:outline-none focus-visible:shadow-[inset_0_0_0_1px_var(--color-brand-neutral-900),3px_3px_0_0_var(--color-brand-blue-600)]";
const outlineButtonStateClasses =
  "border border-brand-neutral-900 bg-brand-neutral-0 text-brand-neutral-900 shadow-none hover:bg-brand-neutral-100 transition-colors duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-blue-600";
const baseButtonStateClasses =
  "shadow-none focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-blue-600";

const variantClasses: Record<ButtonVariant, string> = {
  primary: `border-0 bg-brand-yellow-500 text-brand-neutral-900 ${raisedButtonStateClasses}`,
  secondary: `border-0 bg-brand-neutral-0 text-brand-neutral-900 ${raisedButtonStateClasses}`,
  outline: outlineButtonStateClasses,
  "base-primary": `border border-brand-neutral-900 bg-brand-yellow-500 text-brand-neutral-900 hover:not-disabled:bg-brand-yellow-500/75 ${baseButtonStateClasses}`,
  "base-secondary": `border border-transparent bg-brand-neutral-0 text-brand-neutral-900 hover:not-disabled:border-brand-neutral-900 focus-visible:border-brand-neutral-900 ${baseButtonStateClasses}`,
};

const hidesIcons = computed(() => props.variant.startsWith("base-"));

const classes = computed(() =>
  [
    "inline-flex min-h-11 cursor-pointer items-center justify-center gap-2 rounded-full px-4 py-3 text-preset-4 leading-none transition-[background-color,border-color,box-shadow,outline-color] duration-150 ease-in-out disabled:cursor-not-allowed disabled:opacity-50",
    variantClasses[props.variant],
    props.class,
  ]
    .filter(Boolean)
    .join(" "),
);
</script>

<template>
  <button :type="type" :disabled="disabled" :class="classes">
    <span
      v-if="$slots.iconLeft && !hidesIcons"
      class="inline-flex size-4 shrink-0 text-current [&_svg]:size-full"
    >
      <slot name="iconLeft" />
    </span>

    <span v-if="$slots.default">
      <slot />
    </span>

    <span
      v-if="$slots.iconRight && !hidesIcons"
      class="inline-flex size-4 shrink-0 text-current [&_svg]:size-full"
    >
      <slot name="iconRight" />
    </span>
  </button>
</template>
