import axios from "axios";
import React, { useState } from 'react';
import ReactDOM from 'react-dom';
import PropertiesList from './PropertiesList';
import { Modal } from 'bootstrap';

function Main() {
    const baseURL = 'http://localhost:8000/api/property';
    const [properties, setProperties] = useState([]);
    const [parents, setParents] = useState([]);
    const [children, setChildren] = useState([]);

    React.useEffect(() => {
        getProperties();
      }, []);

    const getProperties = () => {
        axios.get(baseURL).then((response) => {
            setProperties(response.data.data);
        });
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
            console.log(response);
            getProperties();
            resetInputs();
        })
        .catch(function (error) {
            console.log(error);
        })
    }

    const resetInputs = () => {
        const name = document.getElementById('name');
        name.value = '';
        const selectedChildren = document.getElementById('childIds');
        selectedChildren.value = '';
      };

    return (
        <PropertiesList properties={properties} parents={parents} children={children} onAddProperty={addProperty} onSaveProperty={saveProperty}/>
    );
}

export default Main;

if (document.getElementById('main')) {
    ReactDOM.render(<Main />, document.getElementById('main'));
}
