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
        setProperties([]);
        setProperty([]);
        setParents([]);
        setChildren([]);
        getProperties();
      }, []);

    const getProperties = async () => {
        try {
            const response = await axios.get(baseURL);
            setProperties(response.data.data);
        } catch (error) {
            console.log(error.response.data);
        }
    }

    const getProperty = async (property) => {
        try {
            const response = await axios.get(baseURL + '/' + property.name);
            setProperty(response.data.data);
        } catch (error) {
            console.log(error.response.data);
        }

        let getModal = new Modal(document.getElementById('getModal'));
        getModal.show();
    }
    
    const addProperty = async (property) => {
        if (property !== null) {
            try {
                const response = await axios.get(baseURL + '/' + property.name);

                // Parents
                const parentIds = response.data.data.filter((item) => item.relation === 'sibling').map((item) => (item.id));
                const parents = [property];

                // Add parents siblings
                properties.forEach((parent) => {
                    parent.children.forEach((sibling) => {
                        parentIds.forEach((id) => {
                            if (sibling.id === id) {
                                parents.push(sibling);
                            }
                        });
                    });
                });

                // Children
                const children = [];
                
                // Add grandChildren to parent
                parents.forEach((parent) => {
                    parent.children.forEach((child) => {
                        child.children.forEach((grandChild) => {
                            children.push(grandChild);
                        });
                    });
                });

                if (parents.length > 0) {
                    setParents(parents);
                }
                
                if (children.length > 0) {
                    // Remove duplicates and set
                    setChildren([...new Map(children.map(item => [item['id'], item])).values()]);
                }
            } catch (error) {
                console.log(error.response.data);
            }
        } else {
            setParents([]);
            setChildren([]);
        }

        let addModal = new Modal(document.getElementById('addModal'));
        addModal.show();
    }

    const saveProperty = async (saveProperty) => {
        try {
            const response = await axios.post(baseURL, {
                name: saveProperty.name,
                parents: saveProperty.parents,
                children: saveProperty.children,
            });

            getProperties();
            setParents([]);
            setChildren([]);
            resetInputs();
        } catch (error) {
            handleValidationErrors(error);
        }
    }

    const handleValidationErrors = (error) => {
        Object.keys(error.response.data).forEach(k1 => {
            Object.keys(error.response.data[k1]).forEach(k2 => {
                error.response.data[k1][k2].forEach((err) => {
                    alert(err);
                });
            });
        });
    }

    const closeProperty = () => {
        setParents([]);
        setChildren([]);
        resetInputs();
    }

    const resetInputs = () => {
        const name = document.getElementById('name');
        name.value = '';
        const selectedChildren = document.getElementById('childIds');
        selectedChildren.value = '';
      };

    return (
        <PropertiesList properties={properties} property={property} parents={parents} children={children} onAddProperty={addProperty} onSaveProperty={saveProperty} onGetProperty={getProperty} onCloseProperty={closeProperty}/>
    );
}

export default Main;

if (document.getElementById('main')) {
    ReactDOM.render(<Main />, document.getElementById('main'));
}
