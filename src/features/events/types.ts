export type EventItem = {
  id: number;
  title: string;
  date: string;
  type: "upcoming" | "past";
  description: string;
  location: string;
  link?: string;
  image?: string;
  gallery?: string[];
};
