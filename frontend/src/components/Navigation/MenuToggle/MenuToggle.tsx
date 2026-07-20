import './MenuToggle.css'

interface MenuToggleProps {
  onToggle: () => void;
  isOpen: boolean;
}

const MenuToggle = (props: MenuToggleProps) => {
  const classes = ['MenuToggle', 'fas'];

  if (props.isOpen) {
    classes.push('fa-times');
    classes.push('MenuToggle--open');
  } else {
    classes.push('fa-bars');
  }

  return (
    <i
      className={classes.join(' ')}
      onClick={props.onToggle}
    />
  )
}

export default MenuToggle;