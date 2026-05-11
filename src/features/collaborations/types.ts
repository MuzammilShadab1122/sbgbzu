export type Portfolio = {
  id: number;
  title: string;
  description: string;
  image: string;
};

export type Partner = {
  id: number;
  name: string;
  focus: string;
  image?: string;
};

export type University = {
  id: number;
  name: string;
  location: string;
  image?: string;
};