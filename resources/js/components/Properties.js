import axios from "axios";
import React, { useState } from 'react';
import ReactDOM from 'react-dom';
import PropertiesList from './PropertiesList';

const baseURL = 'http://localhost:8000/api/node';

function Properties() {
    const [properties, setProperties] = React.useState([]);

    React.useEffect(() => {
        axios.get(baseURL).then((response) => {
            setProperties(response.data.data);
        });
      }, []);

    return (
        <PropertiesList properties={properties} />
    );
}

export default Properties;

if (document.getElementById('properties')) {
    ReactDOM.render(<Properties />, document.getElementById('properties'));
}
