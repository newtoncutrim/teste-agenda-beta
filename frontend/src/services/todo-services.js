
import axios from "axios";

export default class TodoService {

    static async getAll(){
        return axios.get('/')
    }

    static async addTarefa(params){
       return axios.post("/", params) 
    }

    static async editTarefa(id, params){
        return axios.post( `${id}/update`, params )
    }

    static async getOne(id){
        return axios.get(`/${id}`)
    }

    static async destroy(id){
        return axios.delete(`/${id}`)
    }

    static async login(params){
        return axios.post('/login', params)
    }

    static async register(params){
        return axios.post('/register', params)
    }
}