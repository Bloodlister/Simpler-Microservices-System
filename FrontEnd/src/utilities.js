import axios from 'axios';

export function getUniqueId() {
    return new Promise((resolve) => {
        axios.get('http://users.simple.com/init')
            .then(({data}) => {
                resolve(data.uid);
            });
    })
}
