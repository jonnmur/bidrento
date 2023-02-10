import axios from "axios";
import React, { useState } from 'react';
import ReactDOM from 'react-dom';
import PropertiesList from './components/PropertiesList';
import { Modal } from 'bootstrap';

function Main() {
    const baseURL = 'http://localhost:8000/api/property';
    const [properties, setProperties] = useState([]);
    const [property, setProperty] = useState([]);
    const [parents, setParents] = useState([]);
    const [children, setChildren] = useState([]);

    React.useEffect(() => {
        getProperties();
      }, []);

    const getProperties = () => {
        axios.get(baseURL)
        .then(function (response) {
            setProperties(response.data.data);
        })
        .catch(function (error) {
            console.log(error.response.data);
        });
    }

    const getProperty = (property) => {
        axios.get(baseURL + '/' + property.name)
        .then(function (response) {
            setProperty(response.data.data);
        })
        .catch(function (error) {
            console.log(error.response.data);
        });

        let getModal = new Modal(document.getElementById('getModal'));
        getModal.show();
    }
    
    const addProperty = (property) => {
        let children = [];

        if (property !== null) {
            setParents([property]);

            let siblings = property.children.map((child) =>  child);

            siblings.forEach(sibling => {
                sibling.children.forEach(child => {
                    children.push(child);
                });
            });
        } else {
            setParents([]);
        }

        if (children.length > 0) {
            setChildren([...new Map(children.map(item => [item['id'], item])).values()]);
        } else {
            setChildren([]);
        }
    
        let addModal = new Modal(document.getElementById('addModal'));
        addModal.show();
    }

    const saveProperty = (property) => {
        axios.post(baseURL, {
            name: property.name,
            parents: property.parents,
            children: property.children,
        })
        .then(function (response) {
            getProperties();
            resetInputs();
        })
        .catch(function (error) {
            console.log(error.response.data);
        })
    }

    const resetInputs = () => {
        const name = document.getElementById('name');
        name.value = '';
        const selectedChildren = document.getElementById('childIds');
        selectedChildren.value = '';
      };

    return (
        <PropertiesList properties={properties} property={property} parents={parents} children={children} onAddProperty={addProperty} onSaveProperty={saveProperty} onGetProperty={getProperty}/>
    );
}

export default Main;

if (document.getElementById('main')) {
    ReactDOM.render(<Main />, document.getElementById('main'));
}
