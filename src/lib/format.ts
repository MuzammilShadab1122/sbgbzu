export function fmt(n: number) {
  return new Intl.NumberFormat("en-US").format(n);
}
export function initials(name: string) {
  return name.split(" ").map(p => p[0]).join("").slice(0, 2).toUpperCase();
}