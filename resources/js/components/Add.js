export default function Add({ parents, children, onSaveProperty, onCloseProperty }) {
    const name = document.getElementById('name');
    const selectedParents = document.getElementById('parentIds');
    const selectedChildren = document.getElementById('childIds');

    return (
        <div>
            <div className="modal fade" id="addModal" tabIndex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div className="modal-dialog">
                <div className="modal-content">
                <div className="modal-header">
                    <h1 className="modal-title fs-5" id="addModalLabel">Add property</h1>
                    <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close" onClick={() => onCloseProperty()}></button>
                </div>
                <div className="modal-body">
                <div className="mb-3">
                    <p>Name:</p>
                    <input className="form-control" id="name"></input>

                    <br></br>

                    <p>Parents:</p>
                    <select className="form-select" multiple aria-label="parentIds" id="parentIds">
                    {parents.length > 0 && parents.map((parent) =>
                        <option key={parent.id} value={parent.id}>{parent.name}</option>
                    )}
                    </select>

                    <br></br>

                    <p>Children:</p>
                    <select className="form-select" multiple aria-label="childIds" id="childIds">
                    {children.length > 0 && children.map((child) =>
                        <option key={child.id} value={child.id}>{child.name}</option>
                    )}
                    </select>


                </div>
                </div>
                <div className="modal-footer">
                    <button 
                        type="button"
                        className="btn btn-primary"
                        data-bs-dismiss="modal" 
                        onClick={() => onSaveProperty(
                            {
                                name: name.value,
                                parents: [...selectedParents.selectedOptions].map(option => option.value),
                                children: [...selectedChildren.selectedOptions].map(option => option.value),
                            }
                        )}>
                        Save
                    </button>
                </div>
                </div>
            </div>
            </div>
        </div>
    );
}