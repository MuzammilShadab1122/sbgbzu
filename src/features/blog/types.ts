export type BlogPost = {
  id: number;
  title: string;
  category: "Announcement" | "Event" | "Program" | "Engineering";
  date: string;
  excerpt: string;
};