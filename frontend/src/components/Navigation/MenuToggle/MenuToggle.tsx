import './MenuToggle.css'
import { useMainStore } from "@/store";

const MenuToggle = () => {
  const { isOpenedMenu, toggleMenu } = useMainStore();
  const classes = ['MenuToggle', 'fas'];

  if (isOpenedMenu) {
    classes.push('fa-times');
    classes.push('MenuToggle--open');
  } else {
    classes.push('fa-bars');
  }

  return (
    <i
      className={classes.join(' ')}
      onClick={toggleMenu}
    />
  )
}

export default MenuToggle;