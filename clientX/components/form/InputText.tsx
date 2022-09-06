export const InputText = ({ Name, ClassName, ...props }) => {
  return (
    <input
      name={Name}
      className={`${ClassName} text-gray-600 shadow appearance-none border rounded w-full py-2 px-3`}
      {...props}
    />
  );
};

export default InputText;
