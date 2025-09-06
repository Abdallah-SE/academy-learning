/** @type {import('next').NextConfig} */
const nextConfig = {
  // Your Next.js configuration here
  // No next-intl needed
  images: {
    remotePatterns: [
      {
        protocol: 'http',
        hostname: 'localhost',
        port: '8000',
        pathname: '/**',
      },
      {
        protocol: 'https',
        hostname: '**',
      },
    ],
  },
};

export default nextConfig;