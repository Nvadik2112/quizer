import './Select.css'
import type { Option } from "@/types/main.ts";
import type { ChangeEvent } from "react";

interface SelectProps {
  label?: string,
  value: string | number,
  onChange: (value: string) => void,
  options: Option[],
}

const Select = (props: SelectProps) => {
  const htmlFor = `${props.label}-${Math.random()}`;

  const handleChange = (e: ChangeEvent<HTMLSelectElement>) => {
    props.onChange(e.target.value);
  };

  return (
    <div className='Select'>
      <label htmlFor={htmlFor}>{props.label}</label>
      <select
        id={htmlFor}
        value={props.value}
        onChange={handleChange}
      >
        {
          props.options.map((option, index) => {
            return (
              <option
                value={option.value}
                key={index}>
                {option.title}
              </option>
            )
          })
        }
      </select>
    </div>
  )
}

export default Select