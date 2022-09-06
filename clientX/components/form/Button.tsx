export interface ButtonProps {
  Label: string;
  ClassName: string;
}
export const Button: React.FC<ButtonProps> = ({
  ClassName,
  Label,
  ...props
}) => {
  return (
    <button className={`${ClassName}`} {...props}>
      {Label}
    </button>
  );
};

export default Button;
