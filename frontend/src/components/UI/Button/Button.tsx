import './Button.css';

interface ButtonProps {
  type?: string,
  onClick?: () => void;
  disabled?: boolean,
  children: string,
}

const Button = (props: ButtonProps) => {
  const cls = [
    'Button',
    `${props.type ? `Button--${props.type}` : ''}`
  ]

  return (
    <button
      onClick={props.onClick}
      className={cls.join(' ')}
      disabled={props.disabled}
    >
      {props.children}
    </button>
  )
}

export default Button