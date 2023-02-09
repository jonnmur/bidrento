import React from 'react';

export default function Add({ parentsIds, childrenIds }) {
    return (
        <div>
            <div className="modal fade" id="addModal" tabIndex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div className="modal-dialog">
                <div className="modal-content">
                <div className="modal-header">
                    <h1 className="modal-title fs-5" id="addModalLabel">Add property</h1>
                    <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div className="modal-body">
                    {parentsIds}
                    {childrenIds}
                </div>
                <div className="modal-footer">
                    <button type="button" className="btn btn-primary" data-bs-dismiss="modal">Save</button>
                </div>
                </div>
            </div>
            </div>
        </div>
    );
}