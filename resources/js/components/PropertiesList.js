import React, { useState } from 'react';
import Add from './Add';
import { Modal } from 'bootstrap';

var parentsIds = [];
var childrenIds = [];

function openModal(parents = [], children = []) {
    if (parents.length > 0) {
        parentsIds = parents.map((item) =>  item.id);
    }
    
    if (children.length > 0) {
        childrenIds = children.map((item) =>  item.id);
    }

    let addModal = new Modal(document.getElementById('addModal'));
    addModal.show();

    return parentsIds, childrenIds;
}

function getChildren(item, level) {
    return (
        <div>
            {item.children.length > 0 && item.children.map((child) =>
                <div key={child.id}>
                    <div className="row">
                        <div className="col">
                            {level} {child.name}
                        </div>
                        <div className="col">
                        <div onClick={() => openModal(child.parents, child.children)}> + </div>
                        </div>
                    </div>
                    {child.children.length > 0 && getChildren(child, level + ' - ')}
                </div>
            )}
        </div>
    );
}

export default function PropertiesList({ properties }) {
    const [parentsIds, setParentsIds] = React.useState([]);
    const [childrenIds, setChildrensIds] = React.useState([]);
    const setIds = function(parentsIds, childrenIds) {
        setParentsIds(parentsIds);
        setChildrensIds(childrenIds);
    }
    
    React.useEffect(() => {
        setIds(parentsIds, childrenIds);
      }, []);

    return (
        <div>
            {childrenIds}
            <div className="row">
                <div className="col"></div>
                <div className="col">
                    <div onClick={() => openModal([], [])}> + </div>
                </div>
            </div>
            {properties.length > 0 && properties.map((property) =>
                <div key={property.id}>
                    <div className="row">
                        <div className="col">
                            {property.name}
                        </div>
                        <div className="col">
                        <div onClick={() => setIds(openModal(property.parents, property.children))}> + </div>
                        </div>
                    </div>
                    {getChildren(property, ' - ')}
                </div>
            )}

        <Add parentsIds={parentsIds} childrenIds={childrenIds}/>
        
        </div>
    );
}