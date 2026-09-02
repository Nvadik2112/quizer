import { NavLink } from "react-router-dom";
import './Drawer.css'
import Backdrop from "@/components/UI/Backdrop/Backdrop.tsx";
import { useMainStore } from "@/store";
import { useAuthStore } from "@/store/authStore.ts";
import { LINK_VALUE, type Links} from "@/types/main.ts";

const Drawer = () => {
  const { isOpenedMenu, toggleMenu } = useMainStore();
  const { isAuthenticated, logout } = useAuthStore();

  const links: Links[] = [
    { to: '/', value: LINK_VALUE.LIST, label: 'Список', visible: true },
    { to: '/auth', value: LINK_VALUE.AUTH, label: 'Авторизация', visible: !isAuthenticated },
    { to: '/quiz-creator', value: LINK_VALUE.CREATE, label: 'Создать тест', visible: isAuthenticated },
    { to: '/', value:  LINK_VALUE.LOGOUT, label: 'Выйти', visible: isAuthenticated }
  ];

  const linkList = links.filter((link)=> link.visible);

  const handleLink = (value: string) => {
    if (value === LINK_VALUE.LOGOUT) {
      logout();
    }

    toggleMenu();
  }

  return (
    <>
      <nav className={`Drawer ${ !isOpenedMenu ? `Drawer--close` : ''}`}>
        <ul>
          {linkList.map((link) => (
            <li key={link.to} onClick={() => handleLink(link.value)}>
              <NavLink to={link.to}>
                {link.label}
              </NavLink>
            </li>
          ))}
        </ul>
      </nav>
      { isOpenedMenu ? <Backdrop onClick={toggleMenu} /> : null }
    </>
  )
}

export default Drawer;