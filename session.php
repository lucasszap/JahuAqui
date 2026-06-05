nav a {
  color: #fff;
  text-decoration: none;
  transition: 0.3s;
}

nav a:hover {
  opacity: 0.7;
}

.logo {
  font-size: 24px;
  letter-spacing: 4px;
}


nav {
  display: flex;
  justify-content: space-around;
  align-items: center;
  font-family: system-ui, -apple-system, Helvetica, Arial, sans-serif;
  background: #240046;
  height: 8vh;
}

.nav-list {
  list-style: none;
  display: flex;
  margin-top: 15px;
}

.nav-list .button { display: none; }

.nav-buttons{
  display: flex;
  gap:10px;
} 
.nav-user {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #6c3fc5;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 18px;
}

.user-name {
  color: white;
  font-weight: 600;
}

.btn-cadastrar, .btn-login{
  border: 1px solid #f0f0f0;
  padding: 10px 20px;
  border-radius: 60px;
}

.btn-cadastrar:hover, .btn-login:hover{
  background-color: #fff;
  color: #211b15;
}

.btn-mobile{
  display: none;
}

.nav-list a{
  display: inline-block;
  transition: all 0.3s ease;
}

.nav-list a:hover{
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.nav-list li {
  letter-spacing: 3px;
  margin-left: 32px;
}

.nav-list .nav-buttons{
  display: none;
}

.mobile-menu {
  display: none;
  cursor: pointer;
}

.mobile-menu div {
  width: 32px;
  height: 2px;
  background: #fff;
  margin: 8px;
  transition: 0.3s;
  
}



@media (max-width: 999px) {
  body {
    overflow-x: hidden;
  }
  .nav-list{
    position: absolute;
    top: 6vh;
    right: 0;
    width: 60vw;
    height: 52.5vh;
    background: #240046;
    flex-direction: column;
    align-items: center;
    justify-content: space-around;
    transform: translateX(100%);
    transition: transform 0.3s ease-in;
    z-index: 3;
    font-size: 15px;
  }

  #btn{
    border: 1px solid #f1f2f6;
    border-radius: 30px;
    padding: 20px 15px;
  }

  .nav-buttons {
    display: none;
  }
  
  .nav-list li {
    margin-left: 0;
    opacity: 0;
  }
  .mobile-menu {
    display: block;
  }
}

.nav-list.active {
  transform: translateX(0);
}

@keyframes navLinkFade {
  from {
    opacity: 0;
    transform: translateX(50px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.mobile-menu.active .line1 {
  transform: rotate(-45deg) translate(-8px, 8px);
}

.mobile-menu.active .line2 {
  opacity: 0;
}

.mobile-menu.active .line3 {
  transform: rotate(45deg) translate(-5px, -7px);
}