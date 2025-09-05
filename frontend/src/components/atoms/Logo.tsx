'use client';

import React from 'react';
import Image from 'next/image';

interface LogoProps {
  src: string;
  alt: string;
  width?: number;
  height?: number;
  className?: string;
  priority?: boolean;
}

export const Logo: React.FC<LogoProps> = ({ 
  src, 
  alt, 
  width = 400, 
  height = 400, 
  className = '',
  priority = false 
}) => {
  return (
    <div className={`text-center ${className}`}>
      <Image
        src={src}
        alt={alt}
        width={width}
        height={height}
        className="mx-auto"
        style={{ 
          width: 'auto', 
          height: 'auto',
          maxWidth: '100%',
          maxHeight: '100%'
        }}
        priority={priority}
      />
    </div>
  );
};
