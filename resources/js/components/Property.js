import React from 'react';
import Properties from './Properties';

export default function Property({ property }) {
    return (
        <div>
            {property.name}
        </div>
    );
}