import { createRouter, createWebHistory } from "vue-router";
import HomePage from "../pages/HomePage.vue";
import CarListPage from "../pages/CarListPage.vue";
import BookCarPage from "../pages/BookCarPage.vue";
import ConfirmBookingPage from "../pages/ConfirmBookingPage.vue";
import BookingListPage from "../pages/BookingListPage.vue";

const routes = [
  { path: "/", name: "home", component: HomePage },
  { path: "/cars", name: "cars", component: CarListPage },
  { path: "/book/:id", name: "book", component: BookCarPage },
  { path: "/confirm-booking", name: "confirm", component: ConfirmBookingPage },
  { path: "/bookings", name: "bookings", component: BookingListPage },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
