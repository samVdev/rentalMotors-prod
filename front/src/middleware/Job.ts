export default function Job({ next, storeAuth }) {
  storeAuth.value.role_id != 3
    ? next()
      : next({ path: "not-found-page" });
}
