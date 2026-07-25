import { cva } from "class-variance-authority";

export { default as Button } from "./Button.vue";

export const buttonVariants = cva(
  "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-xl text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive",
  {
    variants: {
      variant: {
        default: "bg-gold-500 text-white hover:bg-gold-600 shadow-md shadow-gold-500/20",
        premium: "bg-gradient-to-br from-gold-400 to-gold-600 text-white shadow-lg shadow-gold-500/30 hover:shadow-gold-500/40 transition-all duration-300 hover:-translate-y-0.5",
        destructive:
          "bg-destructive text-white hover:bg-destructive/90 focus-visible:ring-destructive/20 dark:focus-visible:ring-destructive/40 dark:bg-destructive/60",
        outline:
          "border-2 border-gold-100 bg-background shadow-xs hover:bg-gold-50 hover:text-gold-700 hover:border-gold-200",
        secondary:
          "bg-gold-50 text-gold-700 hover:bg-gold-100",
        ghost:
          "hover:bg-gold-50 hover:text-gold-600",
        link: "text-gold-600 underline-offset-4 hover:underline",
      },
      size: {
        default: "h-11 px-6 has-[>svg]:px-4",
        sm: "h-9 rounded-xl gap-1.5 px-4 has-[>svg]:px-3",
        lg: "h-14 rounded-2xl px-10 has-[>svg]:px-8 text-base",
        icon: "size-11",
        "icon-sm": "size-9",
        "icon-lg": "size-12",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  },
);
