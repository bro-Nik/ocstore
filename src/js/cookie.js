export function addToCookieList(cookieKey, productId, exclude = true, limit = null) {
  // Получаем текущие товары
  var products = getCookie(cookieKey) || [];
  
  const wasInCookies = products.includes(productId.toString());
  // Убираем текущий productId если он уже есть
  products = products.filter(id => id !== productId.toString());
  
  if (!exclude || !wasInCookies) {
    // Добавляем новый productId в начало
    products.unshift(productId.toString());
  }
  
  // Ограничиваем количество
  if (limit && products.length > limit) {
      products = products.slice(0, limit);
  }
  
  setCookie(cookieKey, products.join(','), 30);
  return products.length;
}

// Вспомогательная функция для чтения куки
export function getCookie(name) {
  const cookieValue = readCookie(name);
  if (!cookieValue) return;

  // Пытаемся определить формат
  try {
    // Если значение начинается с { или [, пробуем распарсить как JSON
    if (cookieValue.trim().startsWith('{') || cookieValue.trim().startsWith('[')) {
      return JSON.parse(cookieValue);
    }

    // Если значения через запятую пробуем распарсить
    return cookieValue ? cookieValue.split(',') : [];

  } catch (e) {
    // Если не получилось распарсить как JSON, возвращаем как строку
    console.warn(`Cookie ${name} looks like JSON but parsing failed:`, e);
  }
  
  // Возвращаем строковое значение
  return cookieValue;
}

// Функция установки куки
export function setCookie(name, value, days = 30) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    
    const expires = "expires=" + date.toUTCString();

    // Преобразуем значение в строку
    if (typeof value === 'object' && value !== null) {
        value = JSON.stringify(value);
    }

    document.cookie = name + "=" + encodeURIComponent(value) + ";" + expires + ";path=/;SameSite=Lax";
}

export function clearCookie(name) {
  document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
}

export function addToCartCookie(productId, quantity = 1, options = []) {
  if (!productId) return;
    const cookieKey = 'cart';
    
    // Получаем текущую корзину
    const cartItems = getCookie(cookieKey) || {};
    
    if (cartItems[productId] && quantity == 0) {
      delete cartItems[productId];
    } else if (!cartItems[productId]) {
      // Добавляем новый товар
      cartItems[productId] = {};
    }

    if (cartItems[productId]) {
      // Обновляем количество
      cartItems[productId].quantity = quantity;
      // Обновляем опции
      if (Object.keys(options).length > 0) cartItems[productId].options = options;
      else if (cartItems[productId].options) delete cartItems[productId].options;
    }
    
    // Ограничиваем количество элементов
    const keys = Object.keys(cartItems);
    if (keys.length > 100) {
        // Удаляем самые старые элементы
        const newCartItems = {};
        Object.keys(cartItems)
            .slice(-100)
            .forEach(key => {
                newCartItems[key] = cartItems[key];
            });
        cartItems = newCartItems;
    }
    
    setCookie(cookieKey, cartItems);
    
  return keys.length;
}

function readCookie(name) {
  const nameEQ = name + "=";
  const ca = document.cookie.split(';');
  
  for(let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) === ' ') c = c.substring(1, c.length);
    if (c.indexOf(nameEQ) === 0) {
      return decodeURIComponent(c.substring(nameEQ.length, c.length));
    }
  }
  return null;
}

export function getlistsOfProducts() {
  return {
    cart: Object.keys(getCookie('cart') ?? {}),
    wishlist: getCookie('wishlist') ?? [],
    compare: getCookie('compare') ?? []
  };
}
