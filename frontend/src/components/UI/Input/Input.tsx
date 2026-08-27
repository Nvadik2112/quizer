import './Input.css'
import type {ChangeEvent, HTMLInputTypeAttribute} from "react";

interface InputProps {
  invalid: boolean,
  type: HTMLInputTypeAttribute,
  label: string,
  value: string
  onChange: (value: string) => void,
  errorMessage?: string
}

const Input = (props: InputProps) => {
  const inputType = props.type || 'text';
  const cls = ['Input'];
  const htmlFor = `${inputType}-${Math.random()}`;

  if (props.invalid) {
    cls.push('Input--invalid');
  }

  const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
    props.onChange(e.target.value);
  };

  return (
    <div className={cls.join(' ')}>
        <label htmlFor={htmlFor}>
          {props.label}
        </label>
      <input type={inputType}
             id={htmlFor}
             value={props.value}
             onChange={handleChange}
      />
      {
        props.invalid
          ? <span>{props.errorMessage || 'Введите верное значение'}</span>
          : null
      }
    </div>
  )
}

export default Input;

