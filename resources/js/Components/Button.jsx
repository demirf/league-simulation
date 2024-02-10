import React from 'react';
import { cva } from "class-variance-authority";
const buttonVariants = cva("inline-flex items-center gap-2 font-semibold rounded", {
    variants: {
        variant: {
            primary: "bg-gray-800 hover:bg-gray-900 transition text-white rounded border-transparent",
        },
        size: {
            small: ["text-sm", "py-1", "px-2"],
            medium: ["text-base", "py-2", "px-4"],
        },
    },
    defaultVariants: {
        variant: "primary",
        size: "medium",
    },
});

const Button = React.forwardRef(({ className, variant, size, ...props }, ref) => {
    return (
        (<button
            className={buttonVariants({ variant, size, className })}
            ref={ref}
            {...props} />)
    );
})
Button.displayName = "Button"

export { Button, buttonVariants }
