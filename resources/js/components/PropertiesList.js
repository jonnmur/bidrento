import Add from './Add';

const PropertiesList = ({ properties, onAddProperty, onSaveProperty, parents, children }) => {

    const getChildren = (item, level) => {
        return (
            <div>
                {item.children.length > 0 && item.children.map((child) =>
                    <div key={child.id}>
                        <div className="row">
                            <div className="col">
                                {level} {child.name}
                            </div>
                            <div className="col">
                            <div onClick={() => onAddProperty(child)}> + </div>
                            </div>
                        </div>
                        {child.children.length > 0 && getChildren(child, level + ' - ')}
                    </div>
                )}
            </div>
        );
    }

    return (
        <div>
            <div className="row">
                <div className="col"></div>
                <div className="col">
                    <div onClick={() => onAddProperty(null)}> + </div>
                </div>
            </div>
            {properties.length > 0 && properties.map((property) =>
                <div key={property.id}>
                    <div className="row">
                        <div className="col">
                            {property.name}
                        </div>
                        <div className="col">
                        <div onClick={() => onAddProperty(property)}> + </div>
                        </div>
                    </div>
                    {getChildren(property, ' - ')}
                </div>
            )}
            <Add parents={parents} children={children} onSaveProperty={onSaveProperty}/>
        </div>
    );
}

export default PropertiesList;