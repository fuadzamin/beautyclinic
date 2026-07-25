import { cva } from "class-variance-authority";

export { default as Badge } from "./Badge.vue";

export const badgeVariants = cva(
  "inline-flex gap-1 items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-all focus:outline-none focus:ring-2 focus:ring-gold-500 focus:ring-offset-2",
  {
    variants: {
      variant: {
        default:
          "border-transparent bg-gold-500 text-white shadow-sm",
        secondary:
          "border-transparent bg-gold-50 text-gold-700",
        destructive:
          "border-transparent bg-red-100 text-red-700",
        outline: "text-gold-600 border-gold-200 bg-gold-50/50",
        success: "border-transparent bg-emerald-100 text-emerald-700",
        warning: "border-transparent bg-amber-100 text-amber-700",
      },
    },
    defaultVariants: {
      variant: "default",
    },
  },
);
