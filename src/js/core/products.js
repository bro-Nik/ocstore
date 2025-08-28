import { cart } from './cart';
import { wishlist } from './wishlist';
import { compare } from './compare';
import { getlistsOfProducts } from '../cookie';

export function markProducts(container = document) {
  const listsOfProducts = getlistsOfProducts();
  if (!listsOfProducts) return;

  cart.markProducts(listsOfProducts['cart'] || []);
  wishlist.markProducts(listsOfProducts['wishlist'] || []);
  compare.markProducts(listsOfProducts['compare'] || []);
}
